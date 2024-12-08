<?php

namespace App\Services\User;

use App\Enums\EmployeeType;
use App\Enums\Popluation;
use App\Enums\Status;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class UserService
{
    const KEVA = 1;
    const MILUIM = 2;
    const SADIR = 3;
    const OVED_TZAHAL = 4;


    public function getAllUsers()
    {
        try {

            $users = User::with(['permission' => function ($query) {
                $query->select('id', 'permission_name');
            }])->get();

            if (!$users) {
                return ['status' => Status::NOT_FOUND,];
            }

            // Hide code_permission and is_deleted fields from the permission rows
            $users->each(function ($user) {
                $user->permission->makeHidden(['code_permission', 'is_deleted']);
            });

            return [
                'status' => Status::OK,
                'data' => $users
            ];
        } catch (\Exception $e) {
            logger()->error($e);
        }
        return ['status' => Status::INTERNAL_SERVER_ERROR,];
    }

    public function setNewPermission(Request $request, $id)
    {

        try {

            $user = Auth::user();
            //only user with permission_name of admin.
            if (optional($user->permission)->permission_name !== 'admin') {
                return ['status' => Status::FORBIDDEN,];
            }

            $user_exsist = User::where('id', $id)
                ->where('is_deleted', false)
                ->first();

            if (is_null($user_exsist)) {
                return ['status' => Status::BAD_REQUEST,];
            }

            $user_exsist->update([
                'permission_id' => $request->code_permission
            ]);

            $user_exsist->save();

            return ['status' => Status::OK,];
        } catch (\Exception $e) {
            logger()->error($e->getMessage());
        }
        return ['status' => Status::INTERNAL_SERVER_ERROR,];
    }




    public function searchUser(string $searchString)
    {

        try {

            if (strlen($searchString) === 8) {
                //search user by personal_number
                $user_seach_for = User::with('permission')
                    ->where('personal_number', 'LIKE', '%' . substr($searchString, 1))
                    ->where('is_deleted', false)
                    ->first();

                if ($user_seach_for) {
                    return [
                        'status' => Status::OK,
                        'data' => $user_seach_for
                    ];
                }
            }
            //search users by full name.
            $names = explode(' ', $searchString);

            $users = User::with('permission')
                ->where(function ($query) use ($names) {
                    foreach ($names as $name) {
                        $query->orWhere('name', 'LIKE', '%' . $name . '%');
                    }
                })->get();


            return [
                'status' => Status::OK,
                'data' => $users
            ];
        } catch (\Exception $e) {
            logger()->error($e);
        }


        return ['status' => Status::INTERNAL_SERVER_ERROR,];
    }


    public function createUser(StoreUserRequest $request)
    {

        try {
            $user = Auth::user();
            //only user with permission_name of admin.
            if (optional($user->permission)->permission_name !== 'admin') {
                return ['status' => Status::FORBIDDEN,];
            }

            $personal_number = match ($request->employee_type) {
                EmployeeType::KEVA->value, EmployeeType::SADIR->value => 's' . $request->personal_number,
                EmployeeType::MILUIM->value => 'm' . $request->personal_number,
                EmployeeType::OVED_TZAHAL->value => 'c' . $request->personal_number,
                default => throw new \InvalidArgumentException('סוג עובד לא תקין.')
            };


            $user_exsist = User::where('personal_number',  $personal_number)->where('is_deleted', false)->first();

            if (!is_null($user_exsist)) {
                return ['status' => Status::CONFLICT,];
            }


            $user_exsist = User::where('personal_number', $personal_number)->where('is_deleted', true)->first();

            if (!is_null($user_exsist)) {
                ///need to update the user fileds
                $user_exsist->update([
                    'name' => $request->name,
                    'personal_number' => $personal_number,
                    'email' => "{$personal_number}@army.idf.il",
                    'phone_number' => $request->phone_number,
                    'permission_id' => $request->permission_code, ///set the relation
                    'employee_type' => $request->employee_type,
                    ///not sure need that - need to find out with gal or ilay.
                    'remember_token' => Str::random(10), ///hash random token.
                    'is_deleted' => 0, //back to false.
                ]);
            } else {
                ////create a new uesr from scretch
                $new_user_created = User::create([
                    'name' => $request->name,
                    'personal_number' => $personal_number,
                    'email' => "{$personal_number}@army.idf.il",
                    'phone_number' => $request->phone_number,
                    'permission_id' => $request->permission_code, ///set the relation
                    'employee_type' => $request->employee_type,
                    ///not sure need that - need to find out with gal or ilay.
                    'remember_token' => Str::random(10), ///hash random token.
                ]);
            }

            return ['status' => Status::OK,];
        } catch (\Exception $e) {

            logger()->error($e->getMessage());
        }
        return ['status' => Status::INTERNAL_SERVER_ERROR,];
    }

    public function deleteUser($id)
    {

        try {

            $user = Auth::user();

            //only user with permission_name of admin.
            if (optional($user->permission)->permission_name !== 'admin') {
                return ['status' => Status::FORBIDDEN,];
            }

            $user_exsist = User::where('id', $id)->where('is_deleted', false)->first();

            if (is_null($user_exsist)) {
                return ['status' => Status::NOT_FOUND,];
            }

            //doft deleted user
            $user_exsist->update(['is_deleted' => true]);
            return ['status' => Status::OK,];
        } catch (\Exception $e) {
            logger()->error($e->getMessage());
        }
        return ['status' => Status::INTERNAL_SERVER_ERROR,];
    }
}
