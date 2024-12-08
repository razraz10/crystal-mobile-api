<?php

namespace App\Http\Controllers;

use App\Enums\Status;
use App\Http\Requests\StoreInhibitRequest;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use App\Http\Requests\UpdateInhibitRequest;
use App\Services\Inhibit\InhibitService;


class InhibitController extends Controller
{

    const JAN = 1;
    const DES = 12;
    const BACK_YEAR = 1990;

    protected $_inhibitService;

    public function __construct()
    {
        $this->_inhibitService = new InhibitService();
    }

    /**
     * @OA\Get(
     *      path="/api/inhibits",
     *      tags={"Inhibits"},
     *      summary="Get list of inhibits",
     *      description="Returns a list of inhibits along with their details.",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(
     *                  type="object",
     *                  @OA\Property(property="id", type="integer"),
     *                  @OA\Property(property="inhibit_ta", type="string"),
     *                  @OA\Property(property="inhibit_mrahs", type="string"),
     *                  @OA\Property(property="activ_required", type="string"),
     *                  @OA\Property(property="impacted_tasks", type="string"),
     *                  @OA\Property(property="comment", type="string"),
     *                  @OA\Property(
     *                      property="color_comment",
     *                      type="integer",
     *                      description="Color indication for the inhibit record. Should be between 0 and 3.
     *                1 indicates 'Red', 2 indicates 'Yellow', and 3 indicates 'Green'.",
     *                  ),
     *                  @OA\Property(property="year", type="integer"),
     *                  @OA\Property(property="month", type="integer"),
     *                  @OA\Property(
     *                      property="created_by_user",
     *                      type="object",
     *                      @OA\Property(property="id", type="integer"),
     *                      @OA\Property(property="name", type="string"),
     *                      @OA\Property(property="employee_type", type="integer"),
     *                      @OA\Property(
     *                          property="permission",
     *                          type="object",
     *                          @OA\Property(property="id", type="integer"),
     *                          @OA\Property(property="permission_name", type="string"),
     *                      ),
     *                  ),
     *                  @OA\Property(
     *                      property="updated_by_user",
     *                      type="object",
     *                      @OA\Property(property="id", type="integer"),
     *                      @OA\Property(property="name", type="string"),
     *                      @OA\Property(property="employee_type", type="integer"),
     *                      @OA\Property(
     *                          property="permission",
     *                          type="object",
     *                          @OA\Property(property="id", type="integer"),
     *                          @OA\Property(property="permission_name", type="string"),
     *                      ),
     *                  ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal server error",
     *      )
     * )
     */

    public function index()
    {

        $result = $this->_inhibitService->getAllInhibit();

        // Use match to handle different status cases
        return match ($result['status']) {
            Status::OK => response()->json(['data' => $result['data']], Response::HTTP_OK),
            Status::INTERNAL_SERVER_ERROR => response()->json(['message' => 'התרחש בעיית שרתת יש לנסות שוב מאוחר יותר.'], Response::HTTP_INTERNAL_SERVER_ERROR),
            default => response()->json(['message' => 'Unknown error occurred.'], Response::HTTP_INTERNAL_SERVER_ERROR),
        };
    }

    /**
     * @OA\Get(
     *      path="/api/inhibits/lastuserupdate",
     *      tags={"Inhibits"},
     *      summary="Get last user update information",
     *      description="Returns information about the last user update.",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(
     *                      property="user",
     *                      type="object",
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
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal server error",
     *      )
     * )
     */

    public function getLastUserUpdate()
    {

        $result = $this->_inhibitService->getLastUserUpdateTable();

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
     *      path="/api/inhibits/getinhibitByYearAndMonth",
     *      tags={"Inhibits"},
     *      summary="Get inhibits by year and month",
     *      description="Returns inhibits filtered by year and month.",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"selected_year", "selected_month"},
     *              @OA\Property(property="selected_year", type="integer", example=2023),
     *              @OA\Property(property="selected_month", type="integer", example=11),
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
     *                  @OA\Property(property="inhibit_ta", type="string"),
     *                  @OA\Property(property="inhibit_mrahs", type="string"),
     *                  @OA\Property(property="activ_required", type="string"),
     *                  @OA\Property(property="impacted_tasks", type="string"),
     *                  @OA\Property(property="comment", type="string"),
     *                  @OA\Property(
     *                      property="color_comment",
     *                      type="integer",
     *                      description="Color indication for the inhibit record. Should be between 0 and 3.
     *                   1 indicates 'Red', 2 indicates 'Yellow', and 3 indicates 'Green'.",
     *                  ),
     *                  @OA\Property(property="year", type="integer"),
     *                  @OA\Property(property="month", type="integer"),
     *                  @OA\Property(
     *                      property="created_by_user",
     *                      type="object",
     *                      @OA\Property(property="id", type="integer"),
     *                      @OA\Property(property="name", type="string"),
     *                      @OA\Property(property="employee_type", type="integer"),
     *                      @OA\Property(
     *                          property="permission",
     *                          type="object",
     *                          @OA\Property(property="id", type="integer"),
     *                          @OA\Property(property="permission_name", type="string"),
     *                      ),
     *                  ),
     *                  @OA\Property(
     *                      property="updated_by_user",
     *                      type="object",
     *                      @OA\Property(property="id", type="integer"),
     *                      @OA\Property(property="name", type="string"),
     *                      @OA\Property(property="employee_type", type="integer"),
     *                      @OA\Property(
     *                          property="permission",
     *                          type="object",
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
     *              @OA\Property(property="message", type="string", example="חובה לשלוח שנה תקינה ."),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="חובה לשלוח חודש תקיו ."),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal server error",
     *      )
     * )
     */

    public function getinhibitByYearAndMonth(Request $request)
    {

        $current_year = Carbon::now()->year;
        if ($request->selected_year < self::BACK_YEAR || $request->selected_year > $current_year) {
            return response()->json(['message' => 'חובה לשלוח שנה תקינה .'], Response::HTTP_BAD_REQUEST);
        }
        if ($request->selected_month < self::JAN || $request->selected_month > self::DES) {
            return response()->json(['message' => 'חובה לשלוח חודש תקיו .'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $result = $this->_inhibitService->getinhibitByYearAndMonth($request);

        // Use match to handle different status cases
        return match ($result['status']) {
            Status::OK => response()->json(['data' => $result['data']], Response::HTTP_OK),

            Status::INTERNAL_SERVER_ERROR => response()->json(['message' => 'התרחש בעיית שרתת יש לנסות שוב מאוחר יותר.'], Response::HTTP_INTERNAL_SERVER_ERROR),

            default => response()->json(['message' => 'Unknown error occurred.'], Response::HTTP_INTERNAL_SERVER_ERROR),
        };
    }

    /**
     * @OA\Get(
     *      path="/api/inhibits/{id}",
     *      tags={"Inhibits"},
     *      summary="Get an inhibit by ID",
     *      description="Returns a single inhibit record identified by its ID.",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the inhibit",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="id", type="integer"),
     *              @OA\Property(property="inhibit_ta", type="string"),
     *              @OA\Property(property="inhibit_mrahs", type="string"),
     *              @OA\Property(property="activ_required", type="string"),
     *              @OA\Property(property="impacted_tasks", type="string"),
     *              @OA\Property(property="comment", type="string"),
     *              @OA\Property(
     *                  property="color_comment",
     *                  type="integer",
     *                  description="Color indication for the inhibit record. Should be between 0 and 3.
     *              1 indicates 'Red', 2 indicates 'Yellow', and 3 indicates 'Green'.",
     *              ),
     *              @OA\Property(property="year", type="integer"),
     *              @OA\Property(property="month", type="integer"),
     *              @OA\Property(
     *                  property="created_by_user",
     *                  type="object",
     *                  @OA\Property(property="id", type="integer"),
     *                  @OA\Property(property="name", type="string"),
     *                  @OA\Property(property="employee_type", type="integer"),
     *                  @OA\Property(
     *                      property="permission",
     *                      type="object",
     *                      @OA\Property(property="id", type="integer"),
     *                      @OA\Property(property="permission_name", type="string"),
     *                  ),
     *              ),
     *              @OA\Property(
     *                  property="updated_by_user",
     *                  type="object",
     *                  @OA\Property(property="id", type="integer"),
     *                  @OA\Property(property="name", type="string"),
     *                  @OA\Property(property="employee_type", type="integer"),
     *                  @OA\Property(
     *                      property="permission",
     *                      type="object",
     *                      @OA\Property(property="id", type="integer"),
     *                      @OA\Property(property="permission_name", type="string"),
     *                  ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Inhibit not found",
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal server error",
     *      )
     * )
     */

    public function get($id = null)
    {
        if (!$id) {
            return response(['message' => 'יש לשלוח מספר מזהה של שורה.'], Response::HTTP_BAD_REQUEST);
        }

        $result = $this->_inhibitService->getInhibitById($id);

        // Use match to handle different status cases
        return match ($result['status']) {

            Status::OK => response()->json(['data' => $result['data']], Response::HTTP_OK),

            Status::NOT_FOUND => response()->json(['message' => 'לא נמצאה שורה זו במערכת.'], Response::HTTP_NOT_FOUND),

            Status::INTERNAL_SERVER_ERROR => response()->json(['message' => 'התרחש בעיית שרתת יש לנסות שוב מאוחר יותר.'], Response::HTTP_INTERNAL_SERVER_ERROR),

            default => response()->json(['message' => 'Unknown error occurred.'], Response::HTTP_INTERNAL_SERVER_ERROR),
        };
    }



    /**
     * @OA\Delete(
     *      path="/api/inhibits/{id}",
     *      tags={"Inhibits"},
     *      summary="Delete an inhibit record",
     *      description="Deletes an inhibit record identified by its ID.",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the inhibit record to be deleted",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="שורה נמחקה בהצלחה."),
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
     *          response=403,
     *          description="Forbidden",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="המשתמש אינו מורשה לבצע פעולה זו."),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="לא נמצאה שורה זו במערכת."),
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

    public function delete($id = null)
    {
        if (!$id) {
            return response(['message' => 'יש לשלוח מספר מזהה של שורה.'], Response::HTTP_BAD_REQUEST);
        }

        $result = $this->_inhibitService->deleteInhibit($id);

        // Use match to handle different status cases
        return match ($result['status']) {
            Status::OK => response()->json(['message' => 'שורה נמחקה בהצלחה.'], Response::HTTP_OK),
            Status::BAD_REQUEST => response()->json(['message' => 'לא נמצאה שורה זו במערכת.'], Response::HTTP_BAD_REQUEST),
            Status::FORBIDDEN => response()->json(['message' => 'המשתמש אינו מורשה לבצע פעולה זו.'], Response::HTTP_FORBIDDEN),
            Status::INTERNAL_SERVER_ERROR => response()->json(['message' => 'התרחש בעיית שרתת יש לנסות שוב מאוחר יותר.'], Response::HTTP_INTERNAL_SERVER_ERROR),
            default => response()->json(['message' => 'Unknown error occurred.'], Response::HTTP_INTERNAL_SERVER_ERROR),
        };
    }

    /**
     * @OA\Delete(
     *      path="/api/inhibits/massdelete",
     *      tags={"Inhibits"},
     *      summary="Delete multiple inhibit records",
     *      description="Deletes multiple inhibit records identified by their IDs.",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"ids"},
     *              type="object",
     *              properties={
     *                  @OA\Property(property="ids", type="array", @OA\Items(type="integer")),
     *              },
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="שורות נמחקו בהצלחה."),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad request",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="לא נמצאה שורה זו במערכת."),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="המשתמש אינו מורשה לבצע פעולה זו."),
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

    public function deleteInhibits(Request $request)
    {
        // Validate the request data
        $request->validate([
            'ids' => [
                'required',
                'array',
                Rule::exists('inhibits', 'id')->where(function ($query) {
                    $query->where('is_deleted', 0);
                }),
            ],
        ]);

        $deletedIds = $request->ids;


        $result = $this->_inhibitService->deleteInhibits($deletedIds);

        // Use match to handle different status cases
        return match ($result['status']) {

            Status::OK => response()->json(['message' => 'שורות נמחקו בהצלחה.'], Response::HTTP_OK),

            Status::BAD_REQUEST => response()->json(['message' => 'לא נמצאה שורה זו במערכת.'], Response::HTTP_BAD_REQUEST),

            Status::UNAUTHORIZED => response()->json(['message' => 'המשתמש אינו מורשה לבצע פעולה זו.'], Response::HTTP_UNAUTHORIZED),

            Status::INTERNAL_SERVER_ERROR => response()->json(['message' => 'התרחש בעיית שרתת יש לנסות שוב מאוחר יותר.'], Response::HTTP_INTERNAL_SERVER_ERROR),

            default => response()->json(['message' => 'Unknown error occurred.'], Response::HTTP_INTERNAL_SERVER_ERROR),
        };
    }

    /**
     * @OA\Post(
     *      path="/api/inhibits",
     *      tags={"Inhibits"},
     *      summary="Store a new inhibt row on the table",
     *      description="Create a new inhibt entry.",
     *      @OA\RequestBody(
     *          required=true,
     *          description="inhibts data to store",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  required={"inhibit_ta", "inhibit_mrahs", "year","activ_required", "impacted_tasks", "comment","month"},
     *                  @OA\Property(
     *                      property="inhibit_ta",
     *                      type="string",
     *
     *                      example="this is inhibit text"
     *                  ),
     *                  @OA\Property(
     *                      property="inhibit_mrahs",
     *                      type="string",
     *                      example="this is inhibit mrahs"
     *                  ),
     *                  @OA\Property(
     *                      property="activ_required",
     *                      type="string",
     *                      example="this is active required"
     *                  ),
     *                  @OA\Property(
     *                      property="impacted_tasks",
     *                      type="string",
     *                      example="this is impact text"
     *                  ),
     *                   @OA\Property(
     *                      property="comment",
     *                      type="text",
     *                      example="this is a comment on the this inhibts recoreds"
     *                  ),
     *                  @OA\Property(
     *                      property="color_comment",
     *                      type="integer",
     *                      description="The integer must be between 0-3 1 indeicate Red 2 indeicate Yellow 3 indicate Green",
     *                      example="this is a comment on the this inhibts recoreds"
     *                  ),
     *                 @OA\Property(
     *                      property="month",
     *                      type="integer",
     *                      description="The integer must be number between 1-12 one ondicate on JAN and so on 12 indicate on DEC",
     *                      example="this is a comment on the this inhibts recoreds"
     *                  ),
     *                  @OA\Property(
     *                      property="year",
     *                      type="integer",
     *                      example="1995"
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
     *          response=400,
     *          description="Bad request",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="יש לשלוח שדות חובה ותקינים.")
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

    public function store(StoreInhibitRequest $request)
    {
        ///validate the month request.
        if (!in_array($request->month, range(self::JAN, self::DES))) {
            return response()->json(['error' => 'ערך חודש חייב להיות מספר בין (1-12)'],  Response::HTTP_BAD_REQUEST);
        }


        $result = $this->_inhibitService->createInhibit($request);

        // Use match to handle different status cases
        return match ($result['status']) {
            Status::OK => response()->json(['message' => 'שורה נוצרה בהצלחה.'], Response::HTTP_OK),
            Status::FORBIDDEN => response()->json(['message' => 'המשתמש אינו מורשה לבצע פעולה זו.'], Response::HTTP_FORBIDDEN),
            Status::INTERNAL_SERVER_ERROR => response()->json(['message' => 'התרחש בעיית שרתת יש לנסות שוב מאוחר יותר.'], Response::HTTP_INTERNAL_SERVER_ERROR),
            default => response()->json(['message' => 'Unknown error occurred.'], Response::HTTP_INTERNAL_SERVER_ERROR),
        };
    }


    /**
     * @OA\Put(
     *      path="/api/inhibits/{id}",
     *      tags={"Inhibits"},
     *      summary="Update a inhibit by ID",
     *      description="Update a inhibit row identified by its ID.",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the inhibit to update",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          description="inhibit data to update fileds",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="inhibit_ta",
     *                      type="text",
     *                      example="this is edit of inhibt ta"
     *                  ),
     *                  @OA\Property(
     *                      property="inhibit_mrahs",
     *                      type="text",
     *                      example="this is edit of inhibt mrahs"
     *                  ),
     *                  @OA\Property(
     *                      property="activ_required",
     *                      type="text",
     *                      example="this is edit of active_required filed"
     *                  ),
     *                  @OA\Property(
     *                      property="impacted_tasks",
     *                      type="text",
     *                      example="this is edit of impact_task fileds"
     *                  ),
     *                   @OA\Property(
     *                      property="comment",
     *                      type="text",
     *                      example="this is edit a comment on the this inhibit recoreds"
     *                  ),
     *                 @OA\Property(
     *                      property="year",
     *                      type="integer",
     *                      example="2015"
     *                  ),
     *                   @OA\Property(
     *                      property="color_comment",
     *                      type="integer",
     *                      description="The integer must be between 0-3 1 indeicate Red 2 indeicate Yellow 3 indicate Green",
     *                      example="1"
     *                  ),
     *                   @OA\Property(
     *                      property="month",
     *                      type="integer",
     *                      description="the number must be between 1-12 1 indicate on JAN and so on 12 indicate on DEC",
     *                      example="1"
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
     *              @OA\Property(property="message", type="string", example="שורה זו אינה קיימת במערכת.")
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
     *      ),
     * )
     */


    public function update(UpdateInhibitRequest $request, $id = null)
    {
        if (!$id) {
            return response(['message' => 'יש לשלוח מספר מזהה של שורה.'], Response::HTTP_BAD_REQUEST);
        }

        $result = $this->_inhibitService->updateInhibit($request, $id);

        // Use match to handle different status cases
        return match ($result['status']) {

            Status::OK => response()->json(['message' => 'שורה התעדכנה בהצלחה.'], Response::HTTP_OK),

            Status::FORBIDDEN => response()->json(['message' => 'המשתמש אינו מורשה לבצע פעולה זו.'], Response::HTTP_FORBIDDEN),

            Status::BAD_REQUEST => response()->json(['message' => 'שורה זו אינה קיימת במערכת.'], Response::HTTP_BAD_REQUEST),

            Status::INTERNAL_SERVER_ERROR => response()->json(['message' => 'התרחש בעיית שרתת יש לנסות שוב מאוחר יותר.'], Response::HTTP_INTERNAL_SERVER_ERROR),

            default => response()->json(['message' => 'Unknown error occurred.'], Response::HTTP_INTERNAL_SERVER_ERROR),
        };
    }
}
