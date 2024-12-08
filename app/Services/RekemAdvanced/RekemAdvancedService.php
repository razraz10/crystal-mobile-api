<?php

namespace App\Services\RekemAdvanced;

use App\Enums\Status;
use App\Models\Mission;
use Carbon\Carbon;
use Illuminate\Http\Request;



class RekemAdvancedService
{

    public function getAllRows()
    {
        try {

            $missions = Mission::with([
                'createdByUser.permission:id,permission_name',
                'updatedByUser.permission:id,permission_name'
            ])->where('is_deleted', 0)->get();

            // make sure if any missions were found
            if ($missions->isEmpty()) {

                return ['status' => Status::NOT_FOUND,];
            }

            // Hide fields from the user response
            $missions->makeHidden([
                'month',
                'plan_week_per_month',
                'cumulative_per_month'
            ]);

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

            $latestInhibit = Mission::latest('updated_at')->first();

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


    public function getMissionsByYearAndMonth(Request $request)
    {

        try {

            $missions = Mission::with([
                'createdByUser.permission:id,permission_name',
                'updatedByUser.permission:id,permission_name'
            ])->where('year', $request->selected_year) ///selcted_by year
                ->where('month', $request->selected_month) ///selected by month
                ->where('is_deleted', 0)->get();
            // make sure if any missions were found
            if ($missions->isEmpty()) {

                return ['status' => Status::NOT_FOUND,];
            }
            // Hide fields from the frontend response
            $missions->makeHidden(['month', 'plan_week_per_month', 'cumulative_per_month']);

            return [
                'status' => Status::OK,
                'data' => $missions
            ];
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
            // Hide  fields from the user response
            $mission->makeHidden(['month', 'plan_week_per_month', 'cumulative_per_month']);

            return [
                'data' => $mission,
                'status' => Status::OK,
            ];
        } catch (\Exception $e) {
            logger()->error($e->getMessage());
        }
        return ['status' => Status::INTERNAL_SERVER_ERROR,];
    }
}
