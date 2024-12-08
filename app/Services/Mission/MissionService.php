<?php

namespace App\Services\Mission;

use App\Enums\Month as EnumsMonth;

use App\Enums\Color;
use App\Enums\Status;
use App\Http\Requests\StoreMissionRequest;
use App\Http\Requests\UpdateMissionRequest;
use App\Models\Mission;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class MissionService
{
    const BACK_YEAR = 1990;


    public function getAllMissions()
    {

        try {
            $missions = Mission::with([
                'createdByUser.permission:id,permission_name',
                'updatedByUser.permission:id,permission_name'
            ])->where('is_deleted', 0)->get();

            return [
                'status' => Status::OK,
                'data' => $missions
            ];
        } catch (\Exception $e) {
            logger()->error($e);
        }
        return ['status' => Status::INTERNAL_SERVER_ERROR,];
    }


    public function getLastUserUpdateTable()
    {

        try {

            $latestRowUpdate = Mission::latest('updated_at')->first();

            if (is_null($latestRowUpdate)) {
                return ['status' => Status::NOT_FOUND,];
            }

            $lastUpdatedUser = $latestRowUpdate->updatedByUser()->select('id', 'name')
                ->first();
            $responseData = [
                'user' => [
                    'id' => $lastUpdatedUser->id,
                    'name' => $lastUpdatedUser->name,
                ],
                'updated_at_date' => Carbon::parse($latestRowUpdate->updated_at)->format('Y-m-d'),
                'updated_at_time' => Carbon::parse($latestRowUpdate->updated_at)->format('H:i:s'),
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

    public function getMissionsByYearAndMonth(Request $request)
    {

        $current_year = Carbon::now()->year;
        if ($request->selected_year < self::BACK_YEAR || $request->selected_year > $current_year) {
            return ['status' => Status::BAD_REQUEST,];
        }

        if ($request->selected_month < EnumsMonth::JAN->value || $request->selected_month > EnumsMonth::DEC->value) {
            return ['status' => Status::BAD_REQUEST,];
        }

        try {
            $missions = Mission::with([
                'createdByUser.permission:id,permission_name',
                'updatedByUser.permission:id,permission_name'
            ])->where('year', $request->selected_year)->where('month', $request->selected_month)->where('is_deleted', 0)->get();

            return [
                'data' => $missions,
                'status' => Status::OK,
            ];
        } catch (\Exception $e) {
            logger()->error($e);
        }
        return ['status' => Status::INTERNAL_SERVER_ERROR,];
    }



    public function updateMission(UpdateMissionRequest $request, $id)
    {


        try {

            $user = Auth::user();
            // only user with permission_name 'admin'
            if (optional($user->permission)->permission_name !== 'admin') {
                return ['status' => Status::UNAUTHORIZED,];
            }


            $mission_exisit = Mission::where('id', $id)->where('is_deleted', 0)->first();
            if (!$mission_exisit) {
                return ['status' => Status::NOT_FOUND,];
            }

            $currentTime = Carbon::now()->toDateTimeString();

            $mission_exisit->update([
                'month' => $request->filled('month') ? $request->month : $mission_exisit->month,
                'plan_week_per_month' => $request->filled('plan_week_per_month') ? $request->plan_week_per_month : $mission_exisit->plan_week_per_month,
                'cumulative_per_month' => $request->filled('cumulative_per_month') ? $request->cumulative_per_month : $mission_exisit->cumulative_per_month,
                'year' => $request->filled('year') ? $request->year : $mission_exisit->year,
                'plan_week_per_year' => $request->filled('plan_week_per_year') ? $request->plan_week_per_year : $mission_exisit->plan_week_per_year,
                'cumulative_per_year' => $request->filled('cumulative_per_year') ? $request->cumulative_per_year : $mission_exisit->cumulative_per_year,
                'comment' => $request->filled('comment') ? $request->comment : $mission_exisit->comment,
                'color_comment' => $request->filled('color_comment') ? $request->color_comment : $mission_exisit->color_comment,
                'updated_at' => $currentTime,
                'updated_by' => $user->id,
            ]);

            $mission_exisit->save();

            return ['status' => Status::OK,];
        } catch (\Exception $e) {
            logger()->error($e->getMessage());
        }
        return ['status' => Status::INTERNAL_SERVER_ERROR,];
    }


    public function createMission(StoreMissionRequest $request)
    {



        try {

            $user = Auth::user();
            // only admin with role with permission_name
            if (!$user || optional($user->permission)->permission_name !== 'admin') {
                return ['status' => Status::FORBIDDEN,];
            }

            $currentTime = Carbon::now()->toDateTimeString();

            $mission = Mission::create([
                'comment' => $request->comment,
                'color_comment' => $request->color_comment ? $request->color_comment : Color::GREEN->value,
                'platform' => $request->platform,
                'month' => $request->month,
                'plan_week_per_month' => $request->plan_week_per_month,
                'cumulative_per_month' => $request->cumulative_per_month,
                'year' => $request->year,
                'plan_week_per_year' => $request->plan_week_per_year,
                'cumulative_per_year' => $request->cumulative_per_year,
                'created_by' => $user->id,
                'updated_by' => $user->id,
                'created_at' => $currentTime,
                'updated_at' => $currentTime,

            ]);

            $mission->save();
            return ['status' => Status::CREATED,];
        } catch (\Exception $e) {
            logger()->error($e);
        }
        return ['status' => Status::INTERNAL_SERVER_ERROR,];
    }



    public function getMissionById($id)
    {

        try {


            $mission = Mission::with([
                'createdByUser.permission:id,permission_name',
                'updatedByUser.permission:id,permission_name'
            ])->where('id', $id)->where('is_deleted', 0)->first();

            if (!$mission) {
                return ['status' => Status::NOT_FOUND,];
            }
            return [
                'data' => $mission,
                'status' => Status::OK,
            ];
        } catch (\Exception $e) {
            logger()->error($e->getMessage()); // Log any errors

        }

        return ['status' => Status::INTERNAL_SERVER_ERROR,];
    }


    public function deleteMission($id)
    {


        try {

            $user = Auth::user();
            // only user with permission_name 'admin'
            if (optional($user->permission)->permission_name !== 'admin') {
                return ['status' => Status::FORBIDDEN,];
            }

            $mission = Mission::where('id', $id)->where('is_deleted', 0)->first();
            if (!$mission) {

                return ['status' => Status::NOT_FOUND,];
            }
            //soft delete the mission
            $mission->update(['is_deleted' => true]);
            return ['status' => Status::OK,];
        } catch (\Exception $e) {

            logger()->error($e); ///log any error
        }
        return ['status' => Status::INTERNAL_SERVER_ERROR,];
    }



    public function deleteMissions($deletedIds)
    {

        try {
            $user = Auth::user();
            // only user with permission_name 'admin'
            if (optional($user->permission)->permission_name !== 'admin') {
                return ['status' => Status::FORBIDDEN,];
            }

            // Soft delete the missions rows
            Mission::whereIn('id', $deletedIds)->update(['is_deleted' => true]);
            return ['status' => Status::OK,];
        } catch (\Exception $e) {
            logger()->error($e);
        }
        return ['status' => Status::INTERNAL_SERVER_ERROR,];
    }
}
