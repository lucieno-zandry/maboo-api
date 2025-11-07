<?php

namespace App\Notifications\Transaction;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminTransactionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected ?User $customer;
    protected ?string $type;
    protected ?string $status;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    public function setCustomer(User $customer): AdminTransactionNotification
    {
        $this->customer = $customer;
        return $this;
    }

    public function setType(string $type): AdminTransactionNotification
    {
        $this->type = $type;
        return $this;
    }

    public function setStatus(string $status): AdminTransactionNotification
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
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $informations = [];
        $mail = new MailMessage;

        if ($this->customer) {
            $informations[] = "Nom de l'utilisateur: {$this->customer->name}";
            $informations[] = "Prénom(s) de l'utilisateur: {$this->customer->firstname}";
            $informations[] = "Email de l'utilisateur: {$this->customer->email}";
        }

        if ($this->type)
            $informations[] = "Type de la transaction : {$this->type}";

        if ($this->status)
            $informations[] = "Statut de la transaction: {$this->status}";

        $mail
            ->subject("Nouvelle transaction.")
            ->lines($informations)
            ->action('Voir les détails', url(env('FRONTEND_URL') . "/admin/dashboard/orders/processing"));

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

        $data['title'] = 'Nouvelle transaction';
        $data['line'] = "Type de la transaction : {$this->type}";
        $data['action'] = url(env('FRONTEND_URL') . "/admin/dashboard/orders/processing");

        return $data;
    }
}