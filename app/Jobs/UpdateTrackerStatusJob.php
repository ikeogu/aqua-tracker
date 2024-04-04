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

                $task->update(['status' => Status::OVERDUE->value]);
                $task->farm->owner->notify(new TaskNotification($task, Status::OVERDUE->value));

            }

            if($task->due_date > now()) {

                $task->update(['status' => Status::DUE->value]);
                $task->farm->owner->notify(new TaskNotification($task, Status::DUE->value));

            }

            if($task->due_date == now()) {

                $task->update(['status' => Status::ACTIVE->value]);
                $task->farm->owner->notify(new TaskNotification($task, Status::ACTIVE->value));

            }

            if($task->set_reminder){
                match($task->set_reminder){
                    '5 minutes' => $task->farm->owner->notify(new TaskNotification($task, Status::ACTIVE->value, '5 minutes'))->delay(now()->addMinutes(5)),
                    '10 minutes' => $task->farm->owner->notify(new TaskNotification($task, Status::ACTIVE->value, '10 minutes'))->delay(now()->addMinutes(10)),
                    '15 minutes' => $task->farm->owner->notify(new TaskNotification($task, Status::ACTIVE->value, '15 minutes'))->delay(now()->addMinutes(15)),
                    '30 minutes' => $task->farm->owner->notify(new TaskNotification($task, Status::ACTIVE->value, '30 minutes'))->delay(now()->addMinutes(30)),
                    '1 hour' => $task->farm->owner->notify(new TaskNotification($task, Status::ACTIVE->value, '1 hour'))->delay(now()->addHour()),
                    '2 hours' => $task->farm->owner->notify(new TaskNotification($task, Status::ACTIVE->value, '2 hours'))->delay(now()->addHours(2)),
                    '1 day' => $task->farm->owner->notify(new TaskNotification($task, Status::ACTIVE->value, '1 day'))->delay(now()->addDay()),
                };
            }

        });
    }
}
