<?php

namespace App\Services;

use App\Enums\Status;
use App\Models\Task;
use App\Notifications\TaskNotification;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;


class TaskService
{

    public function executeTask()
    {
        $tasks = Task::where('status', Status::PENDING->value)->get();

        foreach ($tasks as $task) {
            $dueDate = Carbon::parse($task->due_date);
            $startDate = Carbon::parse($task->start_date);
            $now = now();

            // Check if the task is overdue
            if ($dueDate->isPast()) {
                Log::debug(":::::::: task overdue");
                $task->update(['status' => Status::OVERDUE->value]);
                $task->farm->owner->notify(new TaskNotification($task, Status::OVERDUE->value));

                // Handle repeating tasks
                if ($task->repeat) {
                    Log::info(":::::::: task not due");
                    $task->update(['status' => Status::PENDING->value]);
                }
            } elseif ($startDate->isToday()) {
                Log::info(":::::::: task due now");
                $task->update(['status' => Status::ACTIVE->value]);
                $task->farm->owner->notify(new TaskNotification($task, Status::ACTIVE->value));
            }
            // Notification reminders
            $reminderTimes = ['1 day', '3 days', '7 days', '14 days', '30 days', '1 hour', '30 minutes', '15 minutes', '5 minutes', '1 minute'];
            Log::info(":::::::: task reminder");
            foreach ($reminderTimes as $reminderTime) {
                $reminderDate = $dueDate->copy()->sub($reminderTime);
                if ($reminderDate->eq($now)) {
                    Log::debug("Reminder for {$reminderTime} before due date: {$task->id}");
                    $task->farm->owner->notify(new TaskNotification($task, 'pending', $reminderTime));
                }
            }
        }
    }
}