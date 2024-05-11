<?php

namespace App\Notifications;

use App\Models\SubscribedPlan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentInfoNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public SubscribedPlan $subscribedPlan
    )
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Subscription Payment')
            ->from(env('MAIL_FROM_ADDRESS'))
            ->markdown(
                'mails.subscription-payment',
                [
                    'subscribedPlan' => $this->subscribedPlan,
                    'organization' => $this->subscribedPlan->tenant->title,

                ]
            );
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {

        $body = 'Your payment for '. $this->subscribedPlan->subscriptionPlan->title .'plan was successful, and '. $this->subscribedPlan->amount . 'was paid for '. $this->subscribedPlan->no_of_months . 'month(s), and plan starts '.   $this->subscribedPlan->start_date . ' and ends on '.  $this->subscribedPlan->end;
        return [
            //
            'title' => "Subscription made successfully",
            'body' => $body
        ];
    }
}
