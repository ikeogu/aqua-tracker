<?php

namespace App\Jobs;

use App\Enums\Status;
use App\Models\Task;
use App\Notifications\TaskNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateTrackerStatusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $tasks = Task::where('status',Status::PENDING->value)->get();
       
        $tasks->each(function ($task) {

            if ($task->due_date < now()) {
                Log::info(":::::::: task over");
                $task->update(['status' => Status::OVERDUE->value]);
                $task->farm->owner->notify(new TaskNotification($task, Status::OVERDUE->value));

            }

            if ($task->due_date < now() && $task->repeat) {
                Log::info(":::::::: task not due");
                $task->update(['status' => Status::PENDING->value]);


            }

            if($task->due_date > now()) {
                Log::info(":::::::: task due now");
                $task->update(['status' => Status::DUE->value]);
                $task->farm->owner->notify(new TaskNotification($task, Status::DUE->value));

            }

            if($task->due_date == now()) {

                $task->update(['status' => Status::ACTIVE->value]);
                $task->farm->owner->notify(new TaskNotification($task, Status::ACTIVE->value));

            }

            $reminderTimes = ['1 day', '3 days', '7 days', '14 days', '30 days', '1 hr', '30 mins', '15 mins', '5 mins', '1 min'];
            Log::info(":::::::: task reminder");
            foreach ($reminderTimes as $reminderTime) {
                Log::debug($reminderTime);
                if ($task->due_date->sub($reminderTime) == now()) {

                    $task->farm->owner->notify(new TaskNotification($task, 'pending', $reminderTime));

                }

            }

        });
    }
}
