<?php

namespace App\Channels;

use App\Actions\WstokenActions;
use App\Helpers\Helpers;
use Illuminate\Notifications\Notification;
use WebSocket\Client;

class WebSocketChannel
{
    public function send($notifiable, Notification $notification): void
    {
        $token = (new WstokenActions($notifiable->id))->getToken()->uuid;

        if ($token) {
            $url = env('WEBSOCKET_NOTIFICATION_URL') . "/$token";
            $data = method_exists($notification, 'toWebSocket') ?
                $notification->toWebsocket($notifiable) :
                $notification->toArray($notifiable);

            try{
                $server = new Client($url);
                $server->text(json_encode($data));
                $server->close();
            }catch(\Exception $e){
                echo $e->getMessage();
            }
        }
    }
}
