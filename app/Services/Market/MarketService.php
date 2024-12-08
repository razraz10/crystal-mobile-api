<?php

namespace App\Services\Market;

use App\Enums\Color;
use App\Enums\Status;
use App\Http\Requests\StoreMarketRequest;
use App\Http\Requests\UpdateMarketRequest;
use App\Models\Market;
use App\Models\Month;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MarketService
{

    public function getAllMarkets()
    {
        try {
            $markets = Market::with([
                'month',
                'createdByUser.permission:id,permission_name',
                'updatedByUser.permission:id,permission_name'
            ])->where('is_deleted', false)->get();


            return [
                'status' => Status::OK,
                'data' => $markets
            ];
        } catch (\Exception $e) {
            logger()->error($e->getMessage());
        }
        return ['status' => Status::INTERNAL_SERVER_ERROR,];
    }

    public function getMarketById($id = null)
    {

        try {

            $market = Market::with([
                'month',
                'createdByUser.permission:id,permission_name',
                'updatedByUser.permission:id,permission_name'
            ])->where('id', $id)->where('is_deleted', 0)->first();

            if (!$market) {
                return [
                    'status' => Status::NOT_FOUND,

                ];
            }

            return [
                'status' => Status::OK,
                'data' => $market
            ];
        } catch (\Exception $e) {
            logger()->error($e->getMessage());
        }
        return ['status' => Status::INTERNAL_SERVER_ERROR,];
    }

    public function getLastUserUpdateTable()
    {

        try {

            $latestInhibit = Market::latest('updated_at')->first();

            if (is_null($latestInhibit)) {
                return ['status' => Status::NOT_FOUND,];
            }

            $lastUpdatedUser = $latestInhibit->updatedByUser()->select('id', 'name')
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


    public function getMarketsByYear($selected_year)
    {

        try {

            $markets = Market::with([
                'month',
                'createdByUser.permission:id,permission_name',
                'updatedByUser.permission:id,permission_name'
            ])->where('year', $selected_year)->where('is_deleted', false)->get();

            return [
                'status' => Status::OK,
                'data' => $markets
            ];
        } catch (\Exception $e) {

            logger()->error($e->getMessage());
        }
        return ['status' => Status::INTERNAL_SERVER_ERROR,];
    }


    public function updateMarketMonth($month_column, $set_color, $id)
    {


        try {
            $user = Auth::user();

            $market_exisist = Market::with(['month'])
                ->where('id', $id)
                ->where('is_deleted', 0)
                ->first();


            if (!$market_exisist) {
                return ['status' => Status::NOT_FOUND,];
            }

            if (!$market_exisist->month) {
                return ['status' => Status::BAD_REQUEST,];
            }

            DB::beginTransaction(); // Start a database transaction
            // Update the specific month column based on $month_column
            $currentTime = Carbon::now()->toDateTimeString();

            switch ($month_column) {
                case 'JAN':
                    $market_exisist->month->update(['JAN' => $set_color]);
                    break;
                case 'FEB':
                    $market_exisist->month->update(['FEB' => $set_color]);
                    break;
                case 'MAR':
                    $market_exisist->month->update(['MAR' => $set_color]);
                    break;
                case 'APR':
                    $market_exisist->month->update(['APR' => $set_color]);
                    break;
                case 'MAY':
                    $market_exisist->month->update(['MAY' => $set_color]);
                    break;
                case 'JUN':
                    $market_exisist->month->update(['JUN' => $set_color]);
                    break;
                case 'JUL':
                    $market_exisist->month->update(['JUL' => $set_color]);
                    break;
                case 'AUG':
                    $market_exisist->month->update(['AUG' => $set_color]);
                    break;
                case 'SEP':
                    $market_exisist->month->update(['SEP' => $set_color]);
                    break;
                case 'OCT':
                    $market_exisist->month->update(['OCT' => $set_color]);
                    break;
                case 'NOV':
                    $market_exisist->month->update(['NOV' => $set_color]);
                    break;
                case 'DEC':
                    $market_exisist->month->update(['DEC' => $set_color]);
                    break;
                default:
                    // Handle invalid month column
                    return ['status' => Status::BAD_REQUEST];
            }

            $market_exisist->month->updated_at = $currentTime;
            $market_exisist->month->save();

            $market_exisist->updated_by = $user->id;
            $market_exisist->updated_at = $currentTime;
            $market_exisist->save();

            DB::commit(); // commit all changes in database.


            return ['status' => Status::OK,];
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback the transaction in case of any error
            logger()->error($e->getMessage());
        }
        return ['status' => Status::INTERNAL_SERVER_ERROR,];
    }


    public function delete($id)
    {

        try {

            DB::beginTransaction(); // Start a database transaction

            $market = market::with(['month'])->where('id', $id)->where('is_deleted', 0)->first();

            if (!$market) {
                ////market row has not found.
                return ['status' => Status::NOT_FOUND,];
            }
            /////// soft delete the market
            $market->update(['is_deleted' => true,]);
            $market->month ? $market->month->update(['is_deleted' => true]) : null;
            DB::commit(); //commit all changes in database.

            return ['status' => Status::OK,];
        } catch (\Exception $e) {
            DB::rollBack(); //rollback changes.
            logger()->error($e);
        }
        return ['status' => Status::INTERNAL_SERVER_ERROR,];
    }

    public function storeMarketRow(StoreMarketRequest $request)
    {
        //validate the expired_agreement with year fileds.
        $expiredAgreement = Carbon::parse($request->input('expired_agreement'));
        $yearDate = Carbon::createFromFormat('Y', $request->input('year'));

        if ($expiredAgreement->lte($yearDate)) {
            return ['status' => Status::BAD_REQUEST,];
        }

        try {
            $user = Auth::user();


            $market_num_exsist = Market::where('id_num', $request->id_num)->where('is_deleted', 0)->first();

            if ($market_num_exsist) {
                return ['status' => Status::CONFLICT,];
            }

            DB::beginTransaction();

            ////set defaut color - assume it is GREEN.
            $months = Month::create([
                'JAN' => Color::GREEN->value,
                'FEB' => Color::GREEN->value,
                'MAR' => Color::GREEN->value,
                'APR' => Color::GREEN->value,
                'MAY' => Color::GREEN->value,
                'JUN' => Color::GREEN->value,
                'JUL' => Color::GREEN->value,
                'AUG' => Color::GREEN->value,
                'SEP' => Color::GREEN->value,
                'OCT' => Color::GREEN->value,
                'NOV' => Color::GREEN->value,
                'DEC' => Color::GREEN->value,
            ]);

            $months->save();
            $currentTime = Carbon::now()->toDateTimeString();

            $market = Market::create([
                'id_num' => $request->id_num,
                'name_meshek' => $request->name_meshek,
                'year' => $request->year,
                'expired_agreement' => $request->expired_agreement,
                'color_comment' => $request->color_comment ? $request->color_comment : Color::GREEN->value,
                'comment' => $request->comment,
                'is_open' => $request->is_open,
                'month_id' => $months->id, ///set relation
                'created_by' => $user->id,
                'updated_by' => $user->id,
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            ]);
            $market->save();

            DB::commit();

            return ['status' => Status::CREATED,];
        } catch (\Exception $e) {
            DB::rollback(); //rollback changes.
            logger()->error($e->getMessage());
        }
        return ['status' => Status::INTERNAL_SERVER_ERROR,];
    }

    public function updateMarketRow(UpdateMarketRequest $request, $id)
    {

        if ($request->filled('year') && $request->filled('expired_agreement')) {
            //validate the expired_agreement with the year.
            $year = $request->input('year');
            $expiredAgreement = Carbon::parse($request->input('expired_agreement'));
            $yearDate = Carbon::createFromFormat('Y', $year);

            if ($expiredAgreement->lte($yearDate)) {
                return ['status' => Status::BAD_REQUEST,];
            }
        }

        try {
            $user = Auth::user();




            $market_exisist = Market::where('id', $id)->where('is_deleted', 0)->first();

            if (!$market_exisist) {
                return ['status' => Status::CONFLICT,];
            }

            if ($request->filled('expired_agreement')) {
                //validate the expired_agreement with the year fileds
                $expiredAgreement = Carbon::parse($request->input('expired_agreement'));
                $existingYearDate = Carbon::createFromFormat('Y', $market_exisist->year);

                if ($expiredAgreement->lte($existingYearDate)) {
                    return ['status' => Status::BAD_REQUEST,];
                }
            }

            if ($request->filled('year')) {

                //validate the year with the year expired_agreement
                $existingExpiredAgreementDate = Carbon::parse($market_exisist->expired_agreement);
                $yearDate = Carbon::createFromFormat('Y', $request->input('year'));

                if ($yearDate->gte($existingExpiredAgreementDate)) {
                    return ['status' => Status::BAD_REQUEST,];
                }
            }

            $currentTime = Carbon::now()->toDateTimeString();

            $market_exisist->update([
                'id_num' => $request->filled('id_num') ? $request->id_num : $market_exisist->id_num,
                'year' => $request->filled('year') ? $request->year : $market_exisist->year,
                'is_open' => $request->filled('is_open') ? $request->is_open : $market_exisist->is_open,
                'expired_agreement' => $request->filled('expired_agreement') ? $request->expired_agreement : $market_exisist->expired_agreement,
                'name_meshek' => $request->filled('name_meshek') ? $request->name_meshek : $market_exisist->name_meshek,
                'comment' => $request->filled('comment') ? $request->comment : $market_exisist->comment,
                'color_comment' => $request->filled('color_comment') ? $request->color_comment : $market_exisist->color_comment,
                'updated_at' => $currentTime,
                'updated_by' => $user->id,
            ]);

            $market_exisist->save();
            return ['status' => Status::OK,];
        } catch (\Exception $e) {
            logger()->error($e->getMessage());
        }
        return ['status' => Status::INTERNAL_SERVER_ERROR,];
    }

    public function deleteMarkets($deletedIds)
    {


        try {

            DB::beginTransaction(); // Start a database transaction
            //soft delete rows request.
            Market::whereIn('id', $deletedIds)->update(['is_deleted' => true]);

            $deletedMarkets = Market::with('month')
                ->whereIn('id', $deletedIds)
                ->get();

            foreach ($deletedMarkets as $market) {
                // Soft delete the associated month
                $market->month ? $market->month->update(['is_deleted' => true]) : null;
            }

            DB::commit(); // commit all changes in database.
            return ['status' => Status::OK,];
        } catch (\Exception $e) {

            DB::rollBack(); // Rollback the transaction in case of any error
            logger()->error($e);
        }
        return ['status' => Status::INTERNAL_SERVER_ERROR,];
    }
}
