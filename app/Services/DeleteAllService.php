<?php

namespace App\Services;

use App\Enums\Status;
use App\Models\Batch;
use App\Models\Expense;
use App\Models\Farm;
use App\Models\Harvest;
use App\Models\HarvestCustomer;
use App\Models\Inventory;
use App\Models\Pond;
use App\Models\Purchase;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DeleteAllService
{


    public function entities(string $entity) : string
    {
        $entities = [
            'tasks' => Task::class,
            'ponds' => Pond::class,
            'farms' => Farm::class,
            'batches' => Batch::class,
            'customers' => HarvestCustomer::class,
            'expenses' => Expense::class,
            'employees' => User::class,
            'inventories' => Inventory::class,
            'harvests' => Harvest::class,
        ];

        return $entities[$entity];
    }


    public function execute(string $entity, array $ids) : void
    {
        $entity = $this->entities($entity);

        match ($entity) {
            Task::class => Task::whereId($ids)->delete(),
            Pond::class => Pond::whereIn('id', $ids)->delete(),
            Farm::class => Farm::whereIn('id', $ids)->delete(),
            Batch::class => $this->deleteBatches($ids),
            HarvestCustomer::class => $this->deleteHarvestsCustomers($ids),
            Expense::class => Expense::whereIn('id', $ids)->delete(),
            User::class => $this->deleteEmployees($ids),
            Inventory::class => Inventory::whereIn('id', $ids)->delete(),
            Harvest::class => $this->deleteHarvests($ids),
        };
    }

    private function deleteBatches(array $ids) : void
    {
        Pond::whereIn('batch_id', $ids)->delete();
        Batch::whereIn('id', $ids)->delete();
    }

    private function deleteHarvests(array $ids) : void
    {
        HarvestCustomer::whereIn('harvest_id', $ids)->delete();
        Purchase::whereIn('harvest_id', $ids)->delete();
        Harvest::whereIn('id', $ids)->each(function ($harvest) {
            $harvest->batch->update(['status' => Status::INSTOCK->value]);
            $harvest->delete();
        });
    }
    private function deleteHarvestsCustomers(array $ids) : void
    {
        HarvestCustomer::whereIn('id', $ids)->delete();
        Purchase::whereIn('harvest_customer_id', $ids)->delete();

    }

    private function deleteEmployees(array $ids) : void
    {
        User::whereIn('id', $ids)->delete();
        DB::table('farm_user')->whereIn('user_id', $ids)->delete();
    }
}
