<?php

namespace App\Http\Controllers;

use App\Enums\Month as EnumsMonth;
use App\Enums\Status;

use App\Services\RekemAdvanced\RekemAdvancedService;
use Carbon\Carbon;
use Illuminate\Http\Request;

use Illuminate\Http\Response;

class RekemAdvancedController extends Controller
{
    const BACK_YEAR = 1990;

    protected $_rekemAdvancedService;

    public function __construct()
    {
        $this->_rekemAdvancedService = new RekemAdvancedService();
    }

    /**
     * @OA\Get(
     *      path="/api/rekemadvanced/",
     *      tags={"Rekem Advanced"},
     *      summary="Get all rows from the Rekem Advanced table",
     *      description="Fetches all rows from the Rekem Advanced table.",
     *      @OA\Response(
     *          response=200,
     *          description="Success response",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(
     *                      type="object",
     *                      @OA\Property(property="id", type="integer", example=1),
     *                      @OA\Property(property="platform", type="string", example="qui"),
     *                      @OA\Property(property="comment", type="string", example="Debitis id pariatur autem id saepe qui alias."),
     *                      @OA\Property(property="color_comment", type="integer", example=1),
     *                      @OA\Property(property="year", type="integer", example=2015),
     *                      @OA\Property(property="plan_week_per_year", type="integer", example=17),
     *                      @OA\Property(property="cumulative_per_year", type="integer", example=9386),
     *                      @OA\Property(
     *                          property="created_by_user",
     *                          type="object",
     *                          @OA\Property(property="id", type="integer", example=6),
     *                          @OA\Property(property="name", type="string", example="Sonya Kling PhD"),
     *                          @OA\Property(
     *                              property="permission",
     *                              type="object",
     *                              @OA\Property(property="id", type="integer", example=2),
     *                              @OA\Property(property="permission_name", type="string", example="edit")
     *                          )
     *                      ),
     *                      @OA\Property(
     *                          property="updated_by_user",
     *                          type="object",
     *                          @OA\Property(property="id", type="integer", example=4),
     *                          @OA\Property(property="name", type="string", example="Ford Morar DVM"),
     *                          @OA\Property(
     *                              property="permission",
     *                              type="object",
     *                              @OA\Property(property="id", type="integer", example=2),
     *                              @OA\Property(property="permission_name", type="string", example="edit")
     *                          )
     *                      )
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal server error",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="התרחש בעיית שרתת יש לנסות שוב מאוחר יותר.")
     *          )
     *      )
     * )
     */


    public function index()
    {

        $result = $this->_rekemAdvancedService->getAllRows();
        // Use match to handle different status cases
        return match ($result['status']) {
            Status::OK => response()->json(['data' => $result['data']], Response::HTTP_OK),
            Status::INTERNAL_SERVER_ERROR => response()->json(['message' => 'התרחש בעיית שרתת יש לנסות שוב מאוחר יותר.'], Response::HTTP_INTERNAL_SERVER_ERROR),
            default => response()->json(['message' => 'Unknown error occurred.'], Response::HTTP_INTERNAL_SERVER_ERROR),
        };
    }

    /**
     * @OA\Get(
     *      path="/api/rekemadvanced/lastuserupdate",
     *      tags={"Rekem Advanced"},
     *      summary="Get the last user update from the Rekem Advanced table",
     *      description="Fetches the last user update from the Rekem Advanced table, including the associated user information and the timestamp of the update.",
     *      @OA\Response(
     *          response=200,
     *          description="Success response",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(
     *                      property="user",
     *                      type="object",
     *                      @OA\Property(property="id", type="integer", example=11),
     *                      @OA\Property(property="name", type="string", example="Nethanel Efraim")
     *                  ),
     *                  @OA\Property(property="updated_at_date", type="string", example="2024-02-20"),
     *                  @OA\Property(property="updated_at_time", type="string", example="22:19:46")
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="לא נמצאו נתונים.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal server error",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="התרחש בעיית שרתת יש לנסות שוב מאוחר יותר.")
     *          )
     *      )
     * )
     */


    public function getLastUserUpdate()
    {

        $result = $this->_rekemAdvancedService->getLastUserUpdateTable();

        // Use match to handle different status cases
        return match ($result['status']) {
            Status::OK => response()->json(['data' => $result['data']], Response::HTTP_OK),
            Status::NOT_FOUND => response()->json(['message' => 'לא נמצאו נתונים.'], Response::HTTP_NOT_FOUND),
            Status::INTERNAL_SERVER_ERROR => response()->json(['message' => 'התרחש בעיית שרתת יש לנסות שוב מאוחר יותר.'], Response::HTTP_INTERNAL_SERVER_ERROR),
            default => response()->json(['message' => 'Unknown error occurred.'], Response::HTTP_INTERNAL_SERVER_ERROR),
        };
    }


    /**
     * @OA\Get(
     *      path="/api/rekemadvanced/byyearandmonth",
     *      tags={"Rekem Advanced"},
     *      summary="Get missions by year and month",
     *      description="Retrieves missions based on the specified year and month.",
     *      @OA\RequestBody(
     *          required=true,
     *          description="JSON payload containing the selected year and month",
     *          @OA\JsonContent(
     *              required={"selected_month", "selected_year"},
     *              @OA\Property(property="selected_month", type="integer", example=6),
     *              @OA\Property(property="selected_year", type="integer", example=2023),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(
     *                  type="object",
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="platform", type="string", example="qui"),
     *                  @OA\Property(property="comment", type="string", example="Debitis id pariatur autem id saepe qui alias."),
     *                  @OA\Property(property="color_comment", type="integer", example=1),
     *                  @OA\Property(property="year", type="integer", example=2015),
     *                  @OA\Property(property="plan_week_per_year", type="integer", example=17),
     *                  @OA\Property(property="cumulative_per_year", type="integer", example=9386),
     *                  @OA\Property(
     *                      property="created_by_user",
     *                      type="object",
     *                      @OA\Property(property="id", type="integer", example=6),
     *                      @OA\Property(property="name", type="string", example="Sonya Kling PhD"),
     *                      @OA\Property(property="employee_type", type="integer", example=1),
     *                      @OA\Property(
     *                          property="permission",
     *                          type="object",
     *                          @OA\Property(property="id", type="integer", example=2),
     *                          @OA\Property(property="permission_name", type="string", example="edit"),
     *                      ),
     *                  ),
     *                  @OA\Property(
     *                      property="updated_by_user",
     *                      type="object",
     *                      @OA\Property(property="id", type="integer", example=4),
     *                      @OA\Property(property="name", type="string", example="Ford Morar DVM"),
     *                      @OA\Property(property="employee_type", type="integer", example=4),
     *                      @OA\Property(
     *                          property="permission",
     *                          type="object",
     *                          @OA\Property(property="id", type="integer", example=2),
     *                          @OA\Property(property="permission_name", type="string", example="edit"),
     *                      ),
     *                  ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad request",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="חובה לשלוח ערכים של תאריך לחיפוש תקינים."),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal server error",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="התרחש בעיית שרתת יש לנסות שוב מאוחר יותר."),
     *          ),
     *      )
     * )
     */



    public function getMissionsByYearAndMonth(Request $request)
    {
        ////validate the selected_year & selceted_month.
        $current_year = Carbon::now()->year;
        if ($request->selected_year < self::BACK_YEAR || $request->selected_year > $current_year) {
            return response()->json(['error' => 'חובה לשלוח שנה תקינה .'], Response::HTTP_BAD_REQUEST);
        }

        if ($request->selected_month < EnumsMonth::JAN->value || $request->selected_month > EnumsMonth::DEC->value) {
            return response()->json(['error' => 'חובה לשלוח חודש תקיו .'], Response::HTTP_BAD_REQUEST);
        }


        $result = $this->_rekemAdvancedService->getMissionsByYearAndMonth($request);

        // Use match to handle different status cases
        return match ($result['status']) {
            Status::OK => response()->json(['data' => $result['data']], Response::HTTP_OK),
            Status::NOT_FOUND => response()->json(['message' => 'לא נמצאו נתונים.'], Response::HTTP_NOT_FOUND),
            Status::INTERNAL_SERVER_ERROR => response()->json(['message' => 'התרחש בעיית שרתת יש לנסות שוב מאוחר יותר.'], Response::HTTP_INTERNAL_SERVER_ERROR),
            default => response()->json(['message' => 'Unknown error occurred.'], Response::HTTP_INTERNAL_SERVER_ERROR),
        };
    }

    /**
     * @OA\Get(
     *      path="/api/rekemadvanced/{id}",
     *      tags={"Rekem Advanced"},
     *      summary="Get a mission by ID",
     *      description="Retrieve a mission by its ID.",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the mission to retrieve",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success response",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="id", type="integer", example=7),
     *                  @OA\Property(property="platform", type="string", example="veniam"),
     *                  @OA\Property(property="comment", type="string", example="Fugiat incidunt quia illo animi aut."),
     *                  @OA\Property(property="color_comment", type="integer", example=3),
     *                  @OA\Property(property="year", type="integer", example=2015),
     *                  @OA\Property(property="plan_week_per_year", type="integer", example=40),
     *                  @OA\Property(property="cumulative_per_year", type="integer", example=32141),
     *                  @OA\Property(property="created_by_user", type="object",
     *                      @OA\Property(property="id", type="integer", example=5),
     *                      @OA\Property(property="name", type="string", example="Malachi Hintz Sr."),
     *                      @OA\Property(property="employee_type", type="integer", example=3),
     *                      @OA\Property(property="permission", type="object",
     *                          @OA\Property(property="id", type="integer", example=3),
     *                          @OA\Property(property="permission_name", type="string", example="client"),
     *                      ),
     *                  ),
     *                  @OA\Property(property="updated_by_user", type="object",
     *                      @OA\Property(property="id", type="integer", example=10),
     *                      @OA\Property(property="name", type="string", example="Dr. Adriana Wiza II"),
     *                      @OA\Property(property="employee_type", type="integer", example=3),
     *                      @OA\Property(property="permission", type="object",
     *                          @OA\Property(property="id", type="integer", example=3),
     *                          @OA\Property(property="permission_name", type="string", example="client"),
     *                      ),
     *                  ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad request",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="יש לשלוח מספר מזהה של שורה."),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="לא נמצאה שורה זו במערכת.."),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal server error",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="התרחש בעיית שרתת יש לנסות שוב מאוחר יותר."),
     *          ),
     *      ),
     * )
     */

    public function get($id = null)
    {
        if (!$id) {
            return response(['message' => 'יש לשלוח מספר מזהה של שורה.'], Response::HTTP_BAD_REQUEST);
        }

        $result = $this->_rekemAdvancedService->getMissionById($id);

        // Use match to handle different status cases
        return match ($result['status']) {
            Status::OK => response()->json(['data' => $result['data']], Response::HTTP_OK),
            Status::NOT_FOUND => response()->json(['message' => 'לא נמצאה שורה זו במערכת..'], Response::HTTP_NOT_FOUND),
            Status::INTERNAL_SERVER_ERROR => response()->json(['message' => 'התרחש בעיית שרתת יש לנסות שוב מאוחר יותר.'], Response::HTTP_INTERNAL_SERVER_ERROR),
            default => response()->json(['message' => 'Unknown error occurred.'], Response::HTTP_INTERNAL_SERVER_ERROR),
        };
    }
}
