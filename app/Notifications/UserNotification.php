<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;

class UserNotification extends Notification
{
    use Queueable;

    protected $message;
    /**
     * Create a new notification instance.
     */
    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable)
    {
        return ['database', FcmChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     */
    // Store the notification in the database
    public function toDatabase($notifiable)
    {
        return [
            'message' => $this->message,
            'created_at' => now(),
        ];
    }

    public function toFcm($notifiable)
    {
        return FcmMessage::create()
            ->content([
                'title' => 'Your Notification Title',
                'body' => $this->message,
                // Optionally add an image, sound, etc.
            ])
            ->data([
                'custom_key' => 'custom_value',
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
