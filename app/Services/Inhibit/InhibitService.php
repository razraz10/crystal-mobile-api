<?php

namespace App\Services\Inhibit;

use App\Enums\Month as EnumsMonth;
use App\Enums\Status;
use App\Http\Requests\StoreInhibitRequest;
use App\Http\Requests\UpdateInhibitRequest;
use App\Models\Inhibit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InhibitService
{

    public function getAllInhibit()
    {
        try {

            $inhibits = Inhibit::with([
                'createdByUser.permission:id,permission_name',
                'updatedByUser.permission:id,permission_name'
            ])->where('is_deleted', 0)->get();

            return [
                'status' => Status::OK,
                'data' => $inhibits
            ];
        } catch (\Exception $e) {
            logger()->error($e);
        }

        return ['status' => Status::INTERNAL_SERVER_ERROR,];
    }

    public function getLastUserUpdateTable()
    {

        try {

            $latestInhibit = Inhibit::latest('updated_at')->first();

            if (is_null($latestInhibit)) {
                return ['status' => Status::NOT_FOUND,];
            }

            $lastUpdatedUser = $latestInhibit
                ->updatedByUser()
                ->select('id', 'name')
                ->first();

            $responseData = [
                'user' => [
                    'id' => $lastUpdatedUser->id,
                    'name' => $lastUpdatedUser->name,
                ],

                'updated_at_date' => Carbon::parse($latestInhibit->updated_at)->format('Y-m-d'),
                'updated_at_time' => Carbon::parse($latestInhibit->updated_at)->format('H:i:s'),
            ];


            return [
                'status' => Status::OK,
                'data' => $responseData
            ];
        } catch (\Exception $e) {
            logger()->error($e);
        }

        return ['status' => Status::INTERNAL_SERVER_ERROR,];
    }


    public function getinhibitByYearAndMonth(Request $request)
    {

        try {

            $inhibits = Inhibit::with([
                'createdByUser.permission:id,permission_name',
                'updatedByUser.permission:id,permission_name'
            ])
                ->where('month', $request->selected_month)
                ->where('year', $request->selected_year)
                ->where('is_deleted', 0)->get();

            return [
                'status' => Status::OK,
                'data' => $inhibits
            ];
        } catch (\Exception $e) {
            logger()->error($e);
        }
        return ['status' => Status::INTERNAL_SERVER_ERROR,];
    }




    public function updateInhibit(UpdateInhibitRequest $request, $id)
    {

        try {
            $user = Auth::user();
            // only user with permission_name of admin.
            if (optional($user->permission)->permission_name !== 'admin') {
                return ['status' => Status::FORBIDDEN,];
            }

            $currentTime = Carbon::now()->toDateTimeString();

            $inhibit_exisit = Inhibit::where('id', $id)->where('is_deleted', 0)->first();
            if (!$inhibit_exisit) {
                return ['status' => Status::BAD_REQUEST,];
            }


            $inhibit_exisit->update([
                'month' => $request->filled('month') ? $request->month : $inhibit_exisit->month,
                'year' => $request->filled('year') ? $request->year : $inhibit_exisit->year,
                'inhibit_ta' => $request->filled('inhibit_ta') ? $request->inhibit_ta : $inhibit_exisit->inhibit_ta,
                'inhibit_mrahs' => $request->filled('inhibit_mrahs') ? $request->inhibit_mrahs : $inhibit_exisit->inhibit_mrahs,
                'impacted_tasks' => $request->filled('impacted_tasks') ? $request->impacted_tasks : $inhibit_exisit->impacted_tasks,
                'activ_required' => $request->filled('activ_required') ? $request->activ_required : $inhibit_exisit->activ_required,
                'comment' => $request->filled('comment') ? $request->comment : $inhibit_exisit->comment,
                'color_comment' => $request->filled('color_comment') ? $request->color_comment : $inhibit_exisit->color_comment,
                'updated_at' => $currentTime,
                'updated_by' => $user->id,
            ]);

            $inhibit_exisit->save();
            return ['status' => Status::OK,];
        } catch (\Exception $e) {
            logger()->error($e->getMessage());
        }
        return ['status' => Status::INTERNAL_SERVER_ERROR,];
    }


    public function createInhibit(StoreInhibitRequest $request)
    {

        try {

            $user = Auth::user();

            //only user with permission_name of admin.
            if (optional($user->permission)->permission_name !== 'admin') {
                return ['status' => Status::FORBIDDEN,];
            }


            $currentTime = Carbon::now()->toDateTimeString();
            $inhibit = Inhibit::create([
                'comment' => $request->comment,
                'color_comment' => $request->color_comment ? $request->color_comment : 3,
                'activ_required' => $request->activ_required,
                'inhibit_mrahs' => $request->inhibit_mrahs,
                'inhibit_ta' => $request->inhibit_ta,
                'impacted_tasks' => $request->impacted_tasks,
                'year' => $request->year,
                'month' => $request->month,

                'created_by' => $user->id,
                'updated_by' => $user->id,
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            ]);
            return ['status' => Status::OK,];
        } catch (\Exception $e) {
            logger()->error($e->getMessage());
        }
        return ['status' => Status::INTERNAL_SERVER_ERROR,];
    }


    public function getInhibitById($id)
    {

        try {

            $inhibit = Inhibit::with([
                'createdByUser.permission:id,permission_name',
                'updatedByUser.permission:id,permission_name'
            ])
                ->where('id', $id)
                ->where('is_deleted', 0)
                ->first();


            if (!$inhibit) {
                return ['status' => Status::NOT_FOUND,];
            }

            return [
                'status' => Status::OK,
                'data' => $inhibit
            ];
        } catch (\Exception $e) {
            logger()->error($e->getMessage());
        }
        return ['status' => Status::INTERNAL_SERVER_ERROR,];
    }

    public function deleteInhibit($id)
    {

        try {

            $user = Auth::user();
            // only user with permission_name 'admin'
            if (optional($user->permission)->permission_name !== 'admin') {
                return ['status' => Status::FORBIDDEN,];
            }

            $inhibit = Inhibit::where('id', $id)->where('is_deleted', 0)->first();

            if (!$inhibit) {
                return ['status' => Status::BAD_REQUEST,];
            }
            // soft delete the inhibit
            $inhibit->update(['is_deleted' => true]);

            return ['status' => Status::OK,];
        } catch (\Exception $e) {

            logger()->error($e->getMessage());
        }
        return ['status' => Status::INTERNAL_SERVER_ERROR,];
    }

    public function deleteInhibits($deletedIds)
    {


        try {
            $user = Auth::user();

            // only user with permission_name of admin.
            if (optional($user->permission)->permission_name !== 'admin') {
                return ['status' => Status::FORBIDDEN,];
            }


            // Soft delete the inhibits
            Inhibit::whereIn('id', $deletedIds)->update(['is_deleted' => true]);

            return ['status' => Status::OK,];
        } catch (\Exception $e) {

            logger()->error($e);
        }
        return ['status' => Status::INTERNAL_SERVER_ERROR,];
    }
}
