<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Task $task,
        public string $status,
        public string $reminder = ''
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
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {

        $body  = !empty($this->reminder) ? "Task {$this->task->title} is {$this->status}. You have set a reminder for {$this->reminder}" : "Task {$this->task->title} is {$this->status}";
        return (new MailMessage)
                    ->subject("Task {$this->task->title} is {$this->status}")
                    ->line($body)
                    ->line('Task Reminder');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {

       $body  = !empty($this->reminder) ? "Task {$this->task->title} is {$this->status}. You have set a reminder for {$this->reminder}" : "Task {$this->task->title} is {$this->status}";
        return [
            //
            'title' => "Task {$this->task->title} is {$this->status}",
            'body' => $body,

        ];
    }
}