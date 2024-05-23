<?php

namespace App\Console\Commands;

use App\Jobs\TestSupervisorJob;
use Illuminate\Console\Command;

class TestSupervisorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-supervisor-command';

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
        $this->info("run job tester");
        TestSupervisorJob::dispatch();
        return COMMAND::SUCCESS;

    }
}
