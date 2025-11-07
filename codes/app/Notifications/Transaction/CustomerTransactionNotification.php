<?php

namespace App\Notifications\Transaction;

use App\Actions\WstokenActions;
use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomerTransactionNotification extends Notification implements ShouldQueue
{
    use Queueable;
    protected string $order_id;
        protected string $status;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    public function setOrderId(string $orderId)
    {
        $this->order_id = $orderId;
        return $this;
    }

    public function setStatus(string $status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'websocket', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mail = new MailMessage;
        $actionPath = "";

        switch ($this->status) {
            case Transaction::STATUS_SUCCESS:
                $mail
                    ->subject('Commande confirmée')
                    ->line('Nous vous remercions de votre confiance!')
                    ->success();

                $actionPath = "/orders/list?active=PROCESSING";
                break;

            default:
                $mail
                    ->subject('Paiement echoué!')
                    ->line("Votre commande n'a pas été confirmée en raison de l'échec du paiement.")
                    ->error();

                $actionPath = "/orders/order/{$this->order_id}";
                break;
        }

        $mail
            ->action('voir la commande', url(env('FRONTEND_URL') . $actionPath));

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $data = [];
        $actionPath = "";

        switch ($this->status) {
            case Transaction::STATUS_SUCCESS:
                $data['title'] = 'Commande confirmée';
                $data['line'] = 'Nous vous remercions de votre confiance!';
                $data['icon'] = 'fa fa-check-circle';
                $actionPath = "/orders/list?active=PROCESSING";
                break;

            default:
                $data['title'] = 'Paiement echoué!';
                $data['line'] = "Votre commande n'a pas été confirmée en raison de l'échec du paiement.";
                $data['icon'] = 'fa fa-exclamation-circle';
                $actionPath = "/orders/order/{$this->order_id}";
                break;
        }

        $data['action'] = url(env('FRONTEND_URL') . $actionPath);

        return $data;
    }
}
