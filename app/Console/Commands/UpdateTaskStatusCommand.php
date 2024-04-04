<?php

namespace App\Console\Commands;

use App\Jobs\UpdateTrackerStatusJob;
use Illuminate\Console\Command;

class UpdateTaskStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-task-status-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //

        $this->info("Command Initiated ");
        UpdateTrackerStatusJob::dispatch();

        return COMMAND::SUCCESS;
    }
}
