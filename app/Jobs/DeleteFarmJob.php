<?php

namespace App\Jobs;

use App\Models\Farm;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteFarmJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Farm $farm)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        $this->farm->purchases()->delete();
        $this->farm->harvestcustomers()->delete();
        $this->farm->inventories()->delete();
        $this->farm->harvests()->delete();
        $this->farm->tasks()->delete();
        $this->farm->expenses()->delete();
        $this->farm->batches()->delete();
        $this->farm->ponds()->delete();
        $this->farm->users()->detach();
        $this->farm->beneficiaries()->delete();
        $this->farm->delete();
    }
}
