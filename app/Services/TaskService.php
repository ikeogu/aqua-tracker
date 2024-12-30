<?php

namespace App\Services;

use App\Enums\Status;
use App\Models\Task;
use App\Notifications\TaskNotification;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use DateTime;

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
                $overdueThreshold = $dueDate->copy()->addDay(); // Add 1 day grace period

                if (now()->greaterThan($overdueThreshold)) {
                    Log::debug(":::::::: task overdue");
                    $task->update(['status' => Status::OVERDUE->value]);
                    $task->farm->tenant->user->notify(new TaskNotification($task, Status::OVERDUE->value));
                } else {
                    Log::debug(":::::::: task due");
                    $task->update(['status' => Status::DUE->value]);
                    $task->farm->tenant->user->notify(new TaskNotification($task, Status::DUE->value));
                }

                // Handle repeating tasks
                if ($task->repeat) {
                    Log::info(":::::::: task not due");
                    $task->update(['status' => Status::PENDING->value]);
                }
            } elseif ($startDate->isToday()) {
                Log::info(":::::::: task due now");
                $task->update(['status' => Status::ACTIVE->value]);
                $task->farm->tenant->user->notify(new TaskNotification($task, Status::ACTIVE->value));
            }
            // Notification reminders
            $durations = ['1 day', '3 days', '7 days', '14 days', '30 days', '1 hour', '30 minutes', '15 minutes', '5 minutes', '1 minute'];
            $referenceTime = Carbon::parse($task->start_time);
            Log::info(":::::::: task reminder");
            foreach ($durations as $duration) {
               // $reminderDate = $dueDate->copy()->sub($reminderTime);
                $targetTime = $referenceTime->copy()->add($this->parseDurationToCarbonInterval($duration));
                if ($now->equalTo($targetTime)) {
                    Log::debug("Reminder for {$targetTime} before due date: {$task->id}");
                    $task->farm->tenant->user->notify(new TaskNotification($task, 'pending', $targetTime));
                }
            }
        }
    }

        /**
         * Converts a duration string like "1 day" or "30 minutes" into a Carbon interval.
         *
         * @param string $duration
         * @return \DateInterval
         */
        function parseDurationToCarbonInterval(string $duration): \DateInterval
        {
            // Use the 'modify' method of DateTime to parse natural language time expressions
            $date = new DateTime();
            $date->modify($duration);
            $now = new DateTime();

            // Calculate the difference and return as DateInterval
            return $now->diff($date);
        }
}