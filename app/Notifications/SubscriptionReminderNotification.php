<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionReminderNotification extends Notification
{
    use Queueable;

    public $mess;

    /**
     * Create a new notification instance.
     */
    public function __construct(public mixed $days)
    {
        //
        $this->mess = "This is to notify you that your subscription will expire in " . $this->days . " please do well to upgrade or renew your subscription for premium expirence.";
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail','database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {

        return (new MailMessage)
                ->subject('Subscription Expiration Reminder')
                    ->line($this->mess)
                    ->action('Login to Subscribe', env('FRONTEND_URL') .'/login')
                    ->line('Thank you for using our application!');
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
            'title' => 'Subscription Expiration Reminder',
            'body' => $this->mess
        ];
    }
}
