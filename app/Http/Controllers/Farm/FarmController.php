<?php

namespace App\Http\Controllers\Farm;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateFarmRequest;
use Illuminate\Http\Request;

class FarmController extends Controller
{
    //

    public function store(CreateFarmRequest $request)
    {
        $farm = $request->user()->farms()->create($request->validated());

        return $this->success(
            message: 'Farm created successfully',
            code: HttpStatusCode::CREATED->value,
            data: $farm,
        );
    }
}

