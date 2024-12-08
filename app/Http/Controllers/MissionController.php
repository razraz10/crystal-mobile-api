<?php

namespace App\Http\Controllers;

use App\Enums\Status;
use App\Http\Requests\StoreMissionRequest;
use App\Http\Requests\UpdateMissionRequest;
use App\Services\Mission\MissionService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;


class MissionController extends Controller
{



    protected $_missionService;

    public function __construct()
    {
        $this->_missionService = new MissionService();
    }

    /**
     * @OA\Get(
     *      path="/api/missions/",
     *      tags={"Missions"},
     *      summary="Get all missions",
     *      description="Retrieves all missions from the database.",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="array",
     *                  @OA\Items(
     *                      type="object",
     *                      @OA\Property(property="id", type="integer"),
     *                      @OA\Property(property="platform", type="string"),
     *                      @OA\Property(property="comment", type="string"),
     *                      @OA\Property(property="color_comment", type="integer"),
     *                      @OA\Property(property="month", type="integer"),
     *                      @OA\Property(property="plan_week_per_month", type="integer"),
     *                      @OA\Property(property="cumulative_per_month", type="integer"),
     *                      @OA\Property(property="year", type="integer"),
     *                      @OA\Property(property="plan_week_per_year", type="integer"),
     *                      @OA\Property(property="cumulative_per_year", type="integer"),
     *                      @OA\Property(property="created_by_user", type="object",
     *                          @OA\Property(property="id", type="integer"),
     *                          @OA\Property(property="name", type="string"),
     *                          @OA\Property(property="employee_type", type="integer"),
     *                          @OA\Property(property="permission", type="object",
     *                              @OA\Property(property="id", type="integer"),
     *                              @OA\Property(property="permission_name", type="string"),
     *                          ),
     *                      ),
     *                      @OA\Property(property="updated_by_user", type="object",
     *                          @OA\Property(property="id", type="integer"),
     *                          @OA\Property(property="name", type="string"),
     *                          @OA\Property(property="employee_type", type="integer"),
     *                          @OA\Property(property="permission", type="object",
     *                              @OA\Property(property="id", type="integer"),
     *                              @OA\Property(property="permission_name", type="string"),
     *                          ),
     *                      ),
     *                  ),
     *              ),
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

    public function index()
    {

        $result = $this->_missionService->getAllMissions();

        // Use match to handle different status cases
        return match ($result['status']) {
            Status::OK => response()->json(['data' => $result['data']], Response::HTTP_OK),
            Status::INTERNAL_SERVER_ERROR => response()->json(['message' => 'התרחש בעיית שרתת יש לנסות שוב מאוחר יותר.'], Response::HTTP_INTERNAL_SERVER_ERROR),
            default => response()->json(['message' => 'Unknown error occurred.'], Response::HTTP_INTERNAL_SERVER_ERROR),
        };
    }

    /**
     * @OA\Get(
     *      path="/api/missions/lastuserupdate",
     *      operationId="getLastUserUpdate",
     *      tags={"Missions"},
     *      summary="Get last user update",
     *      description="Retrieves the information about the user who last updated a mission record along with the timestamp of the update.",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="user", type="object",
     *                      @OA\Property(property="id", type="integer"),
     *                      @OA\Property(property="name", type="string"),
     *                  ),
     *                  @OA\Property(property="updated_at_date", type="string", format="date"),
     *                  @OA\Property(property="updated_at_time", type="string", format="time"),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="User not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="לא נמצא משתמש."),
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

    public function getLastUserUpdate()
    {

        $result = $this->_missionService->getLastUserUpdateTable();

        // Use match to handle different status cases
        return match ($result['status']) {

            Status::OK => response()->json(['data' => $result['data']], Response::HTTP_OK),

            Status::NOT_FOUND => response()->json(['message' => 'לא נמצא משתמש.'], Response::HTTP_NOT_FOUND),

            Status::INTERNAL_SERVER_ERROR => response()->json(['message' => 'התרחש בעיית שרתת יש לנסות שוב מאוחר יותר.'], Response::HTTP_INTERNAL_SERVER_ERROR),

            default => response()->json(['message' => 'Unknown error occurred.'], Response::HTTP_INTERNAL_SERVER_ERROR),
        };
    }

    /**
     * @OA\Get(
     *      path="/api/missions/byyearandmonth",
     *      operationId="getMissionsByYearAndMonth",
     *      tags={"Missions"},
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
     *                  @OA\Property(property="id", type="integer"),
     *                  @OA\Property(property="platform", type="string"),
     *                  @OA\Property(property="comment", type="string"),
     *                  @OA\Property(property="color_comment", type="integer"),
     *                  @OA\Property(property="month", type="integer"),
     *                  @OA\Property(property="plan_week_per_month", type="integer"),
     *                  @OA\Property(property="cumulative_per_month", type="integer"),
     *                  @OA\Property(property="year", type="integer"),
     *                  @OA\Property(property="plan_week_per_year", type="integer"),
     *                  @OA\Property(property="cumulative_per_year", type="integer"),
     *                  @OA\Property(property="created_by_user", type="object",
     *                      @OA\Property(property="id", type="integer"),
     *                      @OA\Property(property="name", type="string"),
     *                      @OA\Property(property="employee_type", type="integer"),
     *                      @OA\Property(property="permission", type="object",
     *                          @OA\Property(property="id", type="integer"),
     *                          @OA\Property(property="permission_name", type="string"),
     *                      ),
     *                  ),
     *                  @OA\Property(property="updated_by_user", type="object",
     *                      @OA\Property(property="id", type="integer"),
     *                      @OA\Property(property="name", type="string"),
     *                      @OA\Property(property="employee_type", type="integer"),
     *                      @OA\Property(property="permission", type="object",
     *                          @OA\Property(property="id", type="integer"),
     *                          @OA\Property(property="permission_name", type="string"),
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
        $result = $this->_missionService->getMissionsByYearAndMonth($request);

        // Use match to handle different status cases
        return match ($result['status']) {
            Status::OK => response()->json(['data' => $result['data']], Response::HTTP_OK),
            Status::BAD_REQUEST => response()->json(['message' => 'חובה לשלוח ערכים של תאריך לחיפוש תקינים.'], Response::HTTP_BAD_REQUEST),
            Status::INTERNAL_SERVER_ERROR => response()->json(['message' => 'התרחש בעיית שרתת יש לנסות שוב מאוחר יותר.'], Response::HTTP_INTERNAL_SERVER_ERROR),
            default => response()->json(['message' => 'Unknown error occurred.'], Response::HTTP_INTERNAL_SERVER_ERROR),
        };
    }

    /**
     * @OA\Post(
     *      path="/api/missions",
     *      tags={"Missions"},
     *      summary="Store a new mission records in the missions table",
     *      description="Create a new mission entry.",
     *      @OA\RequestBody(
     *          required=true,
     *          description="mission data to store",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  required={"platform", "comment","cumulative_per_year", "plan_week_per_year", "cumulative_per_month","plan_week_per_month","year","month"},
     *                  @OA\Property(
     *                      property="platform",
     *                      type="text",
     *                      example="this is pateform fileds string"
     *                  ),
     *                  @OA\Property(
     *                      property="cumulative_per_year",
     *                      type="integer",
     *                      example="1254"
     *                  ),
     *                  @OA\Property(
     *                      property="plan_week_per_year",
     *                      type="integer",
     *                      example="452"
     *                  ),
     *                  @OA\Property(
     *                      property="cumulative_per_month",
     *                      type="integer",
     *                      example="475"
     *                  ),
     *                  @OA\Property(
     *                      property="plan_week_per_month",
     *                      type="integer",
     *                      example="4756"
     *                  ),
     *                  @OA\Property(
     *                      property="year",
     *                      type="integer",
     *                      description="the integer must be between 1990-2099",
     *                      example="2022"
     *                  ),
     *                  @OA\Property(
     *                      property="month",
     *                      type="integer",
     *                      description="The in teger must be number between 1-12 1 idicate on JAN and 2 indicate on FEB and so on for the rest of the number",
     *                      example="11"
     *                  ),
     *                   @OA\Property(
     *                      property="comment",
     *                      type="text",
     *                      description="this is a comment fileds on this records",
     *                      example="this is a comment on the this mission recoreds"
     *                  ),
     *                   @OA\Property(
     *                      property="color_comment",
     *                      type="text",
     *                      description="The integer must be between 0-3 1 indeicate Red 2 indeicate Yellow 3 indicate Green",
     *                      example="this is a comment on the this mission recoreds"
     *                  ),
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successfully created",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="שורה נוצרה בהצלחה.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="המשתמש אינו מורשה לבצע פעולה זו.")
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


    public function store(StoreMissionRequest $request)
    {

        $result = $this->_missionService->createMission($request);

        // Use match to handle different status cases
        return match ($result['status']) {
            Status::CREATED => response()->json(['message' => 'שורה נוצרה בהצלחה'], Response::HTTP_CREATED),
            Status::FORBIDDEN => response()->json(['message' => 'המשתמש אינו מורשה לבצע פעולה זו.'], Response::HTTP_FORBIDDEN),
            Status::INTERNAL_SERVER_ERROR => response()->json(['message' => 'התרחש בעיית שרתת יש לנסות שוב מאוחר יותר.'], Response::HTTP_INTERNAL_SERVER_ERROR),
            default => response()->json(['message' => 'Unknown error occurred.'], Response::HTTP_INTERNAL_SERVER_ERROR),
        };
    }

    /**
     * @OA\Put(
     *      path="/api/missions/{id}",
     *      tags={"Missions"},
     *      summary="Update a mission by ID",
     *      description="Updates a mission with the specified ID.",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the mission to update",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          description="Mission data to update fields",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="platform",
     *                      type="string",
     *                      example="Platform name"
     *                  ),
     *                  @OA\Property(
     *                      property="comment",
     *                      type="string",
     *                      example="Mission comment"
     *                  ),
     *                  @OA\Property(
     *                      property="cumulative_per_year",
     *                      type="integer",
     *                      example=1000
     *                  ),
     *                  @OA\Property(
     *                      property="plan_week_per_year",
     *                      type="integer",
     *                      example=20
     *                  ),
     *                  @OA\Property(
     *                      property="cumulative_per_month",
     *                      type="integer",
     *                      example=80
     *                  ),
     *                  @OA\Property(
     *                      property="plan_week_per_month",
     *                      type="integer",
     *                      example=4
     *                  ),
     *                  @OA\Property(
     *                      property="color_comment",
     *                      type="integer",
     *                      description="The integer must be between 0-3. 1 indicates Red, 2 indicates Yellow, 3 indicates Green",
     *                      example=1
     *                  ),
     *                  @OA\Property(
     *                      property="year",
     *                      type="integer",
     *                      example=2023
     *                  ),
     *                  @OA\Property(
     *                      property="month",
     *                      type="integer",
     *                      example=10
     *                  ),
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success response",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="שורה התעדכנה בהצלחה.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad request",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="יש לשלוח מספר מזהה של שורה.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="המשתמש אינו מורשה לבצע פעולה זו.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="לא נמצאה שורה זו במערכת.")
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

    public function update(UpdateMissionRequest $request, $id = null)
    {

        if (!$id) {
            return response(['message' => 'יש לשלוח מספר מזהה של שורה.'], Response::HTTP_BAD_REQUEST);
        }

        $result = $this->_missionService->updateMission($request, $id);

        // Use match to handle different status cases
        return match ($result['status']) {
            Status::OK => response()->json(['message' => 'שורה התעדכנה בהצלחה.'], Response::HTTP_OK),
            Status::UNAUTHORIZED => response()->json(['message' => 'המשתמש אינו מורשה לבצע פעולה זו.'], Response::HTTP_FORBIDDEN),
            Status::BAD_REQUEST => response()->json(['message' => 'לא נמצאה שורה זו במערכת.'], Response::HTTP_BAD_REQUEST),
            Status::INTERNAL_SERVER_ERROR => response()->json(['message' => 'התרחש בעיית שרתת יש לנסות שוב מאוחר יותר.'], Response::HTTP_INTERNAL_SERVER_ERROR),
            default => response()->json(['message' => 'Unknown error occurred.'], Response::HTTP_INTERNAL_SERVER_ERROR),
        };
    }



    /**
     * @OA\Get(
     *      path="/api/missions/{id}",
     *      tags={"Missions"},
     *      summary="Get mission by ID",
     *      description="Retrieves a mission from the database by its ID.",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the mission",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="id", type="integer"),
     *                  @OA\Property(property="platform", type="string"),
     *                  @OA\Property(property="comment", type="string"),
     *                  @OA\Property(property="color_comment", type="integer"),
     *                  @OA\Property(property="month", type="integer"),
     *                  @OA\Property(property="plan_week_per_month", type="integer"),
     *                  @OA\Property(property="cumulative_per_month", type="integer"),
     *                  @OA\Property(property="year", type="integer"),
     *                  @OA\Property(property="plan_week_per_year", type="integer"),
     *                  @OA\Property(property="cumulative_per_year", type="integer"),
     *                  @OA\Property(property="created_by_user", type="object",
     *                      @OA\Property(property="id", type="integer"),
     *                      @OA\Property(property="name", type="string"),
     *                      @OA\Property(property="employee_type", type="integer"),
     *                      @OA\Property(property="permission", type="object",
     *                          @OA\Property(property="id", type="integer"),
     *                          @OA\Property(property="permission_name", type="string"),
     *                      ),
     *                  ),
     *                  @OA\Property(property="updated_by_user", type="object",
     *                      @OA\Property(property="id", type="integer"),
     *                      @OA\Property(property="name", type="string"),
     *                      @OA\Property(property="employee_type", type="integer"),
     *                      @OA\Property(property="permission", type="object",
     *                          @OA\Property(property="id", type="integer"),
     *                          @OA\Property(property="permission_name", type="string"),
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
     *          description="Mission not found",
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
     *      )
     * )
     */

    public function get($id = null)
    {

        if (!$id) {
            return response(['message' => 'יש לשלוח מספר מזהה של שורה.'], Response::HTTP_BAD_REQUEST);
        }

        $result = $this->_missionService->getMissionById($id);

        // Use match to handle different status cases
        return match ($result['status']) {
            Status::OK => response()->json(['data' => $result['data']], Response::HTTP_OK),
            Status::NOT_FOUND => response()->json(['message' => 'לא נמצאה שורה זו במערכת..'], Response::HTTP_NOT_FOUND),
            Status::INTERNAL_SERVER_ERROR => response()->json(['message' => 'התרחש בעיית שרתת יש לנסות שוב מאוחר יותר.'], Response::HTTP_INTERNAL_SERVER_ERROR),
            default => response()->json(['message' => 'Unknown error occurred.'], Response::HTTP_INTERNAL_SERVER_ERROR),
        };
    }

    /**
     * @OA\Delete(
     *      path="/api/missions/{id}",
     *      tags={"Missions"},
     *      summary="Delete a mission by ID",
     *      description="Deletes a mission with the specified ID.",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the mission to delete",
     *          @OA\Schema(type="integer", format="int64")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="שורה נמחקה בהצלחה.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad request",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="חובה לשלוח מספר מזהה של הבקשה.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="המשתמש אינו מורשה לבצע פעולה זו.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="לא נמצאה שורה זו במערכת.")
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

    public function delete($id = null)
    {

        if (!$id) {
            return response()->json(['message' => 'חובה לשלוח מספר מזהה של הבקשה.'], Response::HTTP_BAD_REQUEST);
        }


        $result = $this->_missionService->deleteMission($id);


        // Use match to handle different status cases
        return match ($result['status']) {

            Status::OK => response()->json(['message' => 'שורה נמחקה בהצלחה.'], Response::HTTP_OK),

            Status::FORBIDDEN => response()->json(['message' => 'המשתמש אינו מורשה לבצע פעולה זו.'], Response::HTTP_FORBIDDEN),

            Status::NOT_FOUND => response()->json(['message' => 'לא נמצאה שורה זו במערכת.'], Response::HTTP_NOT_FOUND),

            Status::INTERNAL_SERVER_ERROR => response()->json(['message' => 'התרחש בעיית שרתת יש לנסות שוב מאוחר יותר.'], Response::HTTP_INTERNAL_SERVER_ERROR),

            default => response()->json(['message' => 'Unknown error occurred.'], Response::HTTP_INTERNAL_SERVER_ERROR),
        };
    }

    /**
     * @OA\Delete(
     *      path="/api/missions/massdelete",
     *      tags={"Missions"},
     *      summary="Delete multiple missions by IDs",
     *      description="Deletes multiple missions with the specified IDs.",
     *      @OA\RequestBody(
     *          required=true,
     *          description="IDs of the missions to delete",
     *          @OA\JsonContent(
     *              required={"ids"},
     *              @OA\Property(property="ids", type="array", @OA\Items(type="integer", example=1)),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="שורות נמחקו בהצלחה.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="המשתמש אינו מורשה לבצע פעולה זו.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="לא נמצאה שורה זו במערכת.")
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


    public function deleteMissions(Request $request)
    {
        // Validate the request data
        $request->validate([
            'ids' => [
                'required',
                'array',
                Rule::exists('missions', 'id')->where(function ($query) {
                    $query->where('is_deleted', 0);
                }),
            ],
        ]);

        $deletedIds = $request->ids;
        $result = $this->_missionService->deleteMissions($deletedIds);

        // Use match to handle different status cases
        return match ($result['status']) {
            Status::OK => response()->json(['message' => 'שורות נמחקו בהצלחה.'], Response::HTTP_OK),
            Status::FORBIDDEN => response()->json(['message' => 'המשתמש אינו מורשה לבצע פעולה זו.'], Response::HTTP_FORBIDDEN),
            Status::NOT_FOUND => response()->json(['message' => 'לא נמצאה שורה זו במערכת.'], Response::HTTP_NOT_FOUND),
            Status::INTERNAL_SERVER_ERROR => response()->json(['message' => 'התרחש בעיית שרתת יש לנסות שוב מאוחר יותר.'], Response::HTTP_INTERNAL_SERVER_ERROR),
            default => response()->json(['message' => 'Unknown error occurred.'], Response::HTTP_INTERNAL_SERVER_ERROR),
        };
    }
}
