<?php

namespace App\Http\Controllers\Employee;

use App\Enums\HttpStatusCode;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Mail\EmployeeInviteMail;
use App\Models\Farm;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    //

    public function index(Request $request, Farm $farm)
    {

        $employees = $farm->users()->where('role', '!=', 'FARM_TEAM_OWNER')
            ->when($request->search, function ($query) use ($request) {
                return  $query->where('first_name', 'like', '%' . $request->search . '%')
                    ->orWhere('last_name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('phone_number', 'like', '%' . $request->search . '%');
            })

            ->paginate($request->per_page ?? 20);

        return $this->success(
            message: 'Employees retrieved successfully',
            data: EmployeeResource::collection($employees)->response()->getData(),
            code: HttpStatusCode::SUCCESSFUL->value
        );
    }

    public function store(CreateEmployeeRequest $request, Farm $farm)
    {

         /** @var User $user */
         $user = auth()->user();
         if ($user->hasRole([Role::VIEW_FARMS->value])) {
             return $this->error(
                 message: "unathourized area.",
                 code: HttpStatusCode::FORBIDDEN->value
             );
         }

        $pwd = Str::random(8);
        $user = User::firstOrCreate(
            ['email' => $request->email],
            array_merge(Arr::except($request->validated(), ['role', 'phone_number']), [
                'password' => Hash::make($pwd)
            ])
        );

        $farm->users()->attach($user->id, [
            'role' => Role::getRoleNames($request->role),
            'data' => json_encode(['phone_number' => $request->phone_number])
        ]);

        Mail::to($user->email)->send(new EmployeeInviteMail($user, $pwd, $farm, $request->role));

        return $this->success(
            message: 'Employee added successfully',
            data: new EmployeeResource($farm->users()->find($user->id)),
            code: HttpStatusCode::CREATED->value
        );
    }

    public function update(UpdateEmployeeRequest $request, Farm $farm, User $employee)
    {
         /** @var User $user */
         $user = auth()->user();
         if ($user->hasRole([Role::VIEW_FARMS->value])) {
             return $this->error(
                 message: "unathourized area.",
                 code: HttpStatusCode::FORBIDDEN->value
             );
         }
        $farm->users()->updateExistingPivot($employee->id, [
            'role' => Role::getRoleNames($request->role),
            'data' => json_encode(['phone_number' => $request->phone_number])
        ]);

        $employee->update(Arr::except($request->validated(), ['role', 'phone_number']));

        return $this->success(
            message: 'Employee updated successfully',
            data: new EmployeeResource($farm->users()->find($employee->id)),
            code: HttpStatusCode::SUCCESSFUL->value
        );
    }

    public function destroy(Farm $farm, User $employee)
    {
         /** @var User $user */
         $user = auth()->user();
         if ($user->hasRole([Role::VIEW_FARMS->value])) {
             return $this->error(
                 message: "unathourized area.",
                 code: HttpStatusCode::FORBIDDEN->value
             );
         }
        $farm->users()->detach($employee->id);

        return $this->success(
            message: 'Employee deleted successfully',
            code: HttpStatusCode::SUCCESSFUL->value
        );
    }
}
