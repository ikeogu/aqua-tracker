<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DeactivateActivateNotification extends Notification
{
    use Queueable;
    public $subject;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public bool $status
    )
    {
        //
        $this->subject = ($this->status) ? "Account deactivation" : "Account Activation";
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail',];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $view = ($this->status) ? "mails.deactivate-account" : "mails.activate-account";
        return (new MailMessage)
            ->subject($this->subject)
            ->from(env('MAIL_FROM_ADDRESS'))
            ->markdown($view);
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
            'title' => $this->subject,
            'body' => ''
        ];
    }
}
