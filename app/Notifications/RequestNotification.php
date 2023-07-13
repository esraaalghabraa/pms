<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;

class RequestNotification extends Notification
{
    use Queueable;

    /**
     * RequestNotification constructor.
     * @param array $data
     */
    public function __construct(private array $data)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via()
    {
        return [OneSignalChannel::class];
    }

    /**
     * Get the mail representation of the notification.
    /**
     * @return OneSignalMessage
     */
    public function toOneSignal(){
        $requestData = $this->data['requestData'];
        return OneSignalMessage::create()
            ->setSubject($requestData['senderName']."sent you a request.")
            ->setBody($requestData['requestType']."type of request.");
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
