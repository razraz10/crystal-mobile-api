<?php

namespace App\Http\Controllers;

use App\Enums\Color;
use App\Enums\Month as EnumsMonth;
use App\Enums\Status;
use App\Http\Requests\StoreMarketRequest;
use App\Http\Requests\UpdateMarketRequest;
use App\Models\Market;
use App\Models\Month;
use App\Services\Market\MarketService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MarketController extends Controller
{

    protected $_marketService;

    public function __construct()
    {
        $this->_marketService = new MarketService();
    }

    /**
     * @OA\Get(
     *      path="/api/markets",
     *      tags={"Markets"},
     *      summary="Get list of markets",
     *      description="Returns a list of markets along with their details.",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(
     *                  type="object",
     *                  @OA\Property(property="data", type="object",
     *                      @OA\Property(property="id", type="integer"),
     *                      @OA\Property(property="id_num", type="integer"),
     *                      @OA\Property(property="name_meshek", type="string"),
     *                      @OA\Property(property="comment", type="string"),
     *                      @OA\Property(property="color_comment", type="integer"),
     *                      @OA\Property(property="expired_agreement", type="string", format="date"),
     *                      @OA\Property(property="is_open", type="integer"),
     *                      @OA\Property(property="year", type="integer"),
     *                      @OA\Property(property="month", type="object",
     *                          @OA\Property(property="id", type="integer"),
     *                          @OA\Property(property="JAN", type="integer"),
     *                          @OA\Property(property="FEB", type="integer"),
     *                          @OA\Property(property="MAR", type="integer"),
     *                          @OA\Property(property="APR", type="integer"),
     *                          @OA\Property(property="MAY", type="integer"),
     *                          @OA\Property(property="JUN", type="integer"),
     *                          @OA\Property(property="JUL", type="integer"),
     *                          @OA\Property(property="AUG", type="integer"),
     *                          @OA\Property(property="SEP", type="integer"),
     *                          @OA\Property(property="OCT", type="integer"),
     *                          @OA\Property(property="NOV", type="integer"),
     *                          @OA\Property(property="DEC", type="integer"),
     *                      ),
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
     *      )
     * )
     */

    public function index()
    {

        $result = $this->_marketService->getAllMarkets();

        // Use match to handle different status cases
        return match ($result['status']) {
            Status::OK => response()->json(['data' => $result['data']], Response::HTTP_OK),
            Status::INTERNAL_SERVER_ERROR => response()->json(['message' => 'התרחש בעיית שרתת יש לנסות שוב מאוחר יותר.'], Response::HTTP_INTERNAL_SERVER_ERROR),
            default => response()->json(['message' => 'Unknown error occurred.'], Response::HTTP_INTERNAL_SERVER_ERROR),
        };
    }


    /**
     * @OA\Get(
     *      path="/api/markets/{id}",
     *      tags={"Markets"},
     *      summary="Get market by ID",
     *      description="Returns a single market record identified by its ID.",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the market",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="id", type="integer"),
     *                  @OA\Property(property="id_num", type="integer"),
     *                  @OA\Property(property="name_meshek", type="string"),
     *                  @OA\Property(property="comment", type="string"),
     *                  @OA\Property(
     *                      property="color_comment",
     *                      type="integer",
     *                      description="Color indication for the market record. Should be between 0 and 3.
     *                      1 indicates 'Red', 2 indicates 'Yellow', and 3 indicates 'Green'.",
     *                  ),
     *                  @OA\Property(property="expired_agreement", type="string", format="date"),
     *                  @OA\Property(property="is_open", type="integer"),
     *                  @OA\Property(property="year", type="integer"),
     *                  @OA\Property(property="month", type="object",
     *                      @OA\Property(property="id", type="integer"),
     *                      @OA\Property(property="JAN", type="integer"),
     *                      @OA\Property(property="FEB", type="integer"),
     *                      @OA\Property(property="MAR", type="integer"),
     *                      @OA\Property(property="APR", type="integer"),
     *                      @OA\Property(property="MAY", type="integer"),
     *                      @OA\Property(property="JUN", type="integer"),
     *                      @OA\Property(property="JUL", type="integer"),
     *                      @OA\Property(property="AUG", type="integer"),
     *                      @OA\Property(property="SEP", type="integer"),
     *                      @OA\Property(property="OCT", type="integer"),
     *                      @OA\Property(property="NOV", type="integer"),
     *                      @OA\Property(property="DEC", type="integer"),
     *                  ),
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
     *          description="Not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="שורה לא נמצאה."),
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

        $marketResult = $this->_marketService->getMarketById($id);
        // Use match to handle different status cases
        return match ($marketResult['status']) {

            Status::OK => response()->json(['data' => $marketResult['data']], Response::HTTP_OK),

            Status::NOT_FOUND => response()->json(['message' => 'שורה לא נמצאה.'], Response::HTTP_NOT_FOUND),

            Status::INTERNAL_SERVER_ERROR => response()->json(['message' => 'התרחש בעיית שרתת יש לנסות שוב מאוחר יותר.'], Response::HTTP_INTERNAL_SERVER_ERROR),

            default => response()->json(['message' => 'Unknown error occurred.'], Response::HTTP_INTERNAL_SERVER_ERROR),
        };
    }


    /**
     * @OA\Get(
     *      path="/api/markets/lastuserupdate",
     *      tags={"Markets"},
     *      summary="Get last user update",
     *      description="Returns the record associated with the last user update in the markets table.",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
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
     *          description="Not found",
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

        $reslut = $this->_marketService->getLastUserUpdateTable();
        // Use match to handle different status cases
        return match ($reslut['status']) {

            Status::OK => response()->json(['data' => $reslut['data']], Response::HTTP_OK),

            Status::NOT_FOUND => response()->json(['message' => 'לא נמצא משתמש.'], Response::HTTP_NOT_FOUND),

            Status::INTERNAL_SERVER_ERROR => response()->json(['message' => 'התרחש בעיית שרתת יש לנסות שוב מאוחר יותר.'], Response::HTTP_INTERNAL_SERVER_ERROR),

            default => response()->json(['message' => 'Unknown error occurred.'], Response::HTTP_INTERNAL_SERVER_ERROR),
        };
    }


    /**
     * @OA\Get(
     *      path="/api/markets/byear",
     *      tags={"Markets"},
     *      summary="Get markets by year",
     *      description="Returns markets for the specified year along with their details.",
     *      @OA\Parameter(
     *          name="selected_year",
     *          in="query",
     *          required=true,
     *          description="Year for which markets are to be fetched",
     *          @OA\Schema(
     *              type="integer",
     *              format="int32"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="array",
     *                  @OA\Items(
     *                      type="object",
     *                      @OA\Property(property="id", type="integer"),
     *                      @OA\Property(property="id_num", type="integer"),
     *                      @OA\Property(property="name_meshek", type="string"),
     *                      @OA\Property(property="comment", type="string"),
     *                      @OA\Property(property="color_comment", type="integer"),
     *                      @OA\Property(property="expired_agreement", type="string", format="date"),
     *                      @OA\Property(property="is_open", type="integer"),
     *                      @OA\Property(property="year", type="integer"),
     *                      @OA\Property(property="month", type="object",
     *                          @OA\Property(property="id", type="integer"),
     *                          @OA\Property(property="JAN", type="integer"),
     *                          @OA\Property(property="FEB", type="integer"),
     *                          @OA\Property(property="MAR", type="integer"),
     *                          @OA\Property(property="APR", type="integer"),
     *                          @OA\Property(property="MAY", type="integer"),
     *                          @OA\Property(property="JUN", type="integer"),
     *                          @OA\Property(property="JUL", type="integer"),
     *                          @OA\Property(property="AUG", type="integer"),
     *                          @OA\Property(property="SEP", type="integer"),
     *                          @OA\Property(property="OCT", type="integer"),
     *                          @OA\Property(property="NOV", type="integer"),
     *                          @OA\Property(property="DEC", type="integer"),
     *                      ),
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
     *          response=400,
     *          description="Bad request",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="יש לשלוח שנה לחיפוש."),
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



    public function getMarketsByYear(Request $request)
    {

        if (!$request->selected_year) {
            return response(['message' => 'יש לשלוח שנה לחיפוש.'], Response::HTTP_BAD_REQUEST);
        }
        $current_year = Carbon::now()->year;
        if ($request->selected_year > $current_year) {
            return response(['message' => 'אין לשלוח שנה עתידית לחיפוש.'], Response::HTTP_BAD_REQUEST);
        }

        $reslut = $this->_marketService->getMarketsByYear($request->selected_year);
        // Use match to handle different status cases
        return match ($reslut['status']) {

            Status::OK => response()->json(['data' => $reslut['data']], Response::HTTP_OK),

            Status::INTERNAL_SERVER_ERROR => response()->json(['message' => 'התרחש בעיית שרתת יש לנסות שוב מאוחר יותר.'], Response::HTTP_INTERNAL_SERVER_ERROR),

            default => response()->json(['message' => 'Unknown error occurred.'], Response::HTTP_INTERNAL_SERVER_ERROR),
        };
    }

    /**
     * @OA\Post(
     *      path="/api/markets",
     *      tags={"Markets"},
     *      summary="Store a new market",
     *      description="Create a new market entry.",
     *      @OA\RequestBody(
     *          required=true,
     *          description="Market data to store",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  required={"id_num", "name_meshek", "year","expired_agreement", "comment", "is_open"},
     *                  @OA\Property(
     *                      property="id_num",
     *                      type="integer",
     *
     *                      example="123"
     *                  ),
     *                  @OA\Property(
     *                      property="name_meshek",
     *                      type="string",
     *                      example="name_meshek"
     *                  ),
     *                  @OA\Property(
     *                      property="year",
     *                      type="integer",
     *                      description="the integer must be between 1990-2099",
     *                      example="2019"
     *                  ),
     *                  @OA\Property(
     *                      property="expired_agreement",
     *                      type="date",
     *                      example="2025-01-08"
     *                  ),
     *                   @OA\Property(
     *                      property="comment",
     *                      type="text",
     *                      example="this is a comment on the this market recoreds"
     *                  ),
     *                   @OA\Property(
     *                      property="color_comment",
     *                      type="text",
     *                      description="The integer must be between 0-3 1 indeicate Red 2 indeicate Yellow 3 indicate Green",
     *                      example="this is a comment on the this market recoreds"
     *                  ),
     *                 @OA\Property(
     *                      property="is_open",
     *                      type="bolean",
     *                      example="this is a comment on the this market recoreds"
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
     *          response=409,
     *          description="Conflict",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="שורה זו קיימת במערכת.")
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

    public function store(StoreMarketRequest $request)
    {
        try {

            $user = Auth::user();

            //only user with permission_name admin can create.
            if (optional($user->permission)->permission_name !== 'admin') {
                return response()->json(['message' => 'המשתמש אינו מורשה לבצע פעולה זו.'], Response::HTTP_FORBIDDEN);
            }


            $reslut = $this->_marketService->storeMarketRow($request);
            // Use match to handle different status cases
            return match ($reslut['status']) {
                Status::CREATED => response()->json(['message' => 'שורה נוצרה בהצלחה.'], Response::HTTP_CREATED),
                Status::CONFLICT => response()->json(['message' => 'שורה זו קיימת במערכת.'],  Response::HTTP_CONFLICT),
                Status::INTERNAL_SERVER_ERROR => response()->json(['message' => 'התרחש בעיית שרתת יש לנסות שוב מאוחר יותר.'], Response::HTTP_INTERNAL_SERVER_ERROR),
                default => response()->json(['message' => 'Unknown error occurred.'], Response::HTTP_INTERNAL_SERVER_ERROR),
            };
        } catch (\Exception $e) {
            DB::rollback(); //rollback changes.
            logger()->error($e->getMessage());
        }

        return response()->json([
            'message' => 'בעיית שרת. נסה שוב מאוחר יותר',
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @OA\Put(
     *      path="/api/markets/{id}",
     *      tags={"Markets"},
     *      summary="Update a market by ID",
     *      description="Update a market row identified by its ID.",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the market to update",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          description="Market data to update fields",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="id_num",
     *                      type="integer",
     *                      example=123
     *                  ),
     *                  @OA\Property(
     *                      property="name_meshek",
     *                      type="string",
     *                      example="edit name_meshek"
     *                  ),
     *                  @OA\Property(
     *                      property="year",
     *                      type="integer",
     *                      example=2019
     *                  ),
     *                  @OA\Property(
     *                      property="expired_agreement",
     *                      type="string",
     *                      format="date",
     *                      example="2025-01-08"
     *                  ),
     *                  @OA\Property(
     *                      property="comment",
     *                      type="string",
     *                      example="this is a comment on the market records"
     *                  ),
     *                  @OA\Property(
     *                      property="is_open",
     *                      type="boolean",
     *                      example=true
     *                  ),
     *                  @OA\Property(
     *                      property="color_comment",
     *                      type="integer",
     *                      description="The integer must be between 0-3 (1 indicates Red, 2 indicates Yellow, 3 indicates Green)",
     *                      example=1
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
     *              @OA\Property(property="message", type="string", example="אחת מהשדות אינם תקינים")
     *          )
     *      ),
     *      @OA\Response(
     *          response=409,
     *          description="Conflict",
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
     *      )
     * )
     */

    public function update(UpdateMarketRequest $request, $id = null)
    {

        try {

            $user = Auth::user();
            //only user with permisssion_name admin
            if (optional($user->permission)->permission_name !== 'admin') {
                return response()->json(['message' => 'המשתמש אינו מורשה לבצע פעולה זו.'], Response::HTTP_FORBIDDEN);
            }

            $reslut = $this->_marketService->updateMarketRow($request, $id);
            // Use match to handle different status cases
            return match ($reslut['status']) {

                Status::OK => response()->json(['message' => 'שורה התעדכנה בהצלחה.'], Response::HTTP_OK),

                Status::BAD_REQUEST => response()->json(['message' => 'אחת מהשדות אינם תקינים'], Response::HTTP_BAD_REQUEST),

                Status::CONFLICT => response()->json(['message' => 'שורה זו אינה קיימת במערכת.'], Response::HTTP_CONFLICT),

                Status::INTERNAL_SERVER_ERROR => response()->json(['message' => 'התרחש בעיית שרתת יש לנסות שוב מאוחר יותר.'], Response::HTTP_INTERNAL_SERVER_ERROR),

                default => response()->json(['message' => 'Unknown error occurred.'], Response::HTTP_INTERNAL_SERVER_ERROR),
            };
        } catch (\Exception $e) {
            logger()->error($e->getMessage());
        }

        return response()->json([
            'message' => 'בעיית שרת. נסה שוב מאוחר יותר',
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @OA\Put(
     *      path="/api/markets/updatemonth/{id}",
     *      tags={"Markets"},
     *      summary="Update market month by ID",
     *      description="Update the month of a market row identified by its ID.",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the market to update month",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          description="Updated market month data",
     *          @OA\JsonContent(
     *              required={"selected_month", "set_color"},
     *              @OA\Property(property="selected_month", type="integer", format="int32", example=1, description="Selected month (1-12)"),
     *              @OA\Property(property="set_color", type="integer", format="int32", example=1, description="Set color (1-3)")
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
     *              @OA\Property(property="message", type="string", example="שורה זו אינה קיימת במערכת.")
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

    public function updateMarketMonth(Request $request, $id = null)
    {


        $user = Auth::user();
        //only user with permisssion_name admin
        if (optional($user->permission)->permission_name !== 'admin') {
            return response()->json(['message' => 'המשתמש אינו מורשה לבצע פעולה זו.'], Response::HTTP_FORBIDDEN);
        }

        if (!$id) {
            return response(['message' => 'יש לשלוח מספר מזהה של שורה.'], Response::HTTP_BAD_REQUEST);
        }

        if ($request->selected_month < EnumsMonth::JAN->value || $request->selected_month > EnumsMonth::DEC->value) {
            return response()->json(['message' => 'חובה לשלוח חודש תקיו .'], Response::HTTP_BAD_REQUEST);
        }
        // Validate set_color
        if (!in_array($request->set_color, [Color::GREEN->value, Color::RED->value, Color::YELLOW->value])) {
            return response()->json(['message' => 'ערך צבע שגוי. יש לשלוח ערכים בין 1-3.'], Response::HTTP_BAD_REQUEST);
        }
        $set_color = $request->set_color;
        $month_column = match ($request->selected_month) {
            EnumsMonth::JAN->value => 'JAN',
            EnumsMonth::FEB->value => 'FAB',
            EnumsMonth::MAR->value => 'MAR',
            EnumsMonth::APR->value => 'APR',
            EnumsMonth::MAY->value => 'MAY',
            EnumsMonth::JUN->value => 'JUN',
            EnumsMonth::JUL->value => 'JUL',
            EnumsMonth::AUG->value => 'AUG',
            EnumsMonth::SEP->value => 'SEP',
            EnumsMonth::OCT->value => 'OCT',
            EnumsMonth::NOV->value => 'NOV',
            EnumsMonth::DEC->value => 'DEC',

            default => throw new \InvalidArgumentException('מספר חודש לא תקין.')
        };



        try {

            $user = Auth::user();
            //  only user exists and has permission_name 'admin'
            if (!$user || optional($user->permission)->permission_name !== 'admin') {
                return response()->json(['message' => 'המשתמש אינו מורשה לבצע פעולה זו.'], Response::HTTP_UNAUTHORIZED);
            }

            $reslut = $this->_marketService->updateMarketMonth($month_column, $set_color, $id);

            // Use match to handle different status cases
            return match ($reslut['status']) {
                Status::OK => response()->json(['message' => 'שורה התעדכנה בהצלחה.'], Response::HTTP_OK),
                Status::BAD_REQUEST => response()->json(['message' => 'שורה זו אינה נתנת לעריכה.יש ליצור שורה זאת שוב.'], Response::HTTP_BAD_REQUEST),
                Status::NOT_FOUND => response()->json(['message' => 'שורה זו אינה קיימת במערכת.'], Response::HTTP_NOT_FOUND),
                Status::INTERNAL_SERVER_ERROR => response()->json(['message' => 'התרחש בעיית שרתת יש לנסות שוב מאוחר יותר.'], Response::HTTP_INTERNAL_SERVER_ERROR),
                default => response()->json(['message' => 'Unknown error occurred.'], Response::HTTP_INTERNAL_SERVER_ERROR),
            };
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback the transaction in case of any error
            logger()->error($e->getMessage());
        }

        return response()->json([
            'message' => 'בעיית שרת. נסה שוב מאוחר יותר',
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }


    /**
     * @OA\Delete(
     *      path="/api/markets/{id}",
     *      tags={"Markets"},
     *      summary="Delete a market by ID",
     *      description="Deletes a market with the specified ID.",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the market to delete",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
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
     *              @OA\Property(property="message", type="string", example="שורה לא קיימת במערכת.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal server error",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="בעיית שרת התרחש במערכת.יש לנסות מאוחר יותר")
     *          )
     *      )
     * )
     */


    public function delete($id)
    {


        if (!$id) {
            return response()->json(['message' => 'חובה לשלוח מספר מזהה של הבקשה.'], Response::HTTP_NO_CONTENT);
        }
        try {

            $user = Auth::user();
            //  only user exists and has permission_name 'admin'
            if (optional($user->permission)->permission_name !== 'admin') {
                return response()->json(['message' => 'המשתמש אינו מורשה לבצע פעולה זו.'], Response::HTTP_FORBIDDEN);
            }

            $reslut = $this->_marketService->delete($id);
            // Use match to handle different status cases
            return match ($reslut['status']) {

                Status::OK => response()->json(['message' => 'שורה נמחקה בהצלחה.'], Response::HTTP_OK),

                Status::NOT_FOUND => response()->json(['message' => 'שורה לא קיימת במערכת.'], Response::HTTP_NOT_FOUND),

                Status::INTERNAL_SERVER_ERROR => response()->json(['message' => 'התרחש בעיית שרתת יש לנסות שוב מאוחר יותר.'], Response::HTTP_INTERNAL_SERVER_ERROR),

                default => response()->json(['message' => 'Unknown error occurred.'], Response::HTTP_INTERNAL_SERVER_ERROR),
            };
        } catch (\Exception $e) {
            DB::rollBack(); //rollback changes.
            logger()->error($e);
        }
        return response()->json(['message' => 'בעיית שרת התרחש במערכת.יש לנסות מאוחר יותר'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }


    /**
     * @OA\Delete(
     *      path="/api/markets/massdelete",
     *      tags={"Markets"},
     *      summary="Delete multiple markets",
     *      description="Deletes multiple markets based on the provided IDs.",
     *      @OA\RequestBody(
     *          required=true,
     *          description="Array of market IDs to delete",
     *          @OA\JsonContent(
     *              required={"ids"},
     *              @OA\Property(property="ids", type="array", @OA\Items(type="integer"))
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="שורות נמחקו בהצלחה.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad request",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="The given data was invalid.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Unauthorized",
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
     *              @OA\Property(property="message", type="string", example="One or more market IDs not found.")
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

    public function deleteMarkets(Request $request)
    {
        // Validate the request data
        $request->validate([
            'ids' => [
                'required',
                'array',
                Rule::exists('markets', 'id')->where(function ($query) {
                    $query->where('is_deleted', 0);
                }),
            ],
        ]);

        try {


            $user = Auth::user();
            //  only user exists and has permission_name 'admin'
            if (optional($user->permission)->permission_name !== 'admin') {
                return response()->json(['message' => 'המשתמש אינו מורשה לבצע פעולה זו.'], Response::HTTP_UNAUTHORIZED);
            }

            $reslut = $this->_marketService->deleteMarkets($request->ids);
            // Use match to handle different status cases
            return match ($reslut['status']) {
                Status::OK => response()->json(['message' => 'שורות נמחקו בהצלחה.'], Response::HTTP_OK),
                Status::INTERNAL_SERVER_ERROR => response()->json(['message' => 'התרחש בעיית שרתת יש לנסות שוב מאוחר יותר.'], Response::HTTP_INTERNAL_SERVER_ERROR),
                default => response()->json(['message' => 'Unknown error occurred.'], Response::HTTP_INTERNAL_SERVER_ERROR),
            };
        } catch (\Exception $e) {

            DB::rollBack(); // Rollback the transaction in case of any error
            logger()->error($e);
        }
        return response()->json(['message' => 'בעיית שרת התרחשה במערכת. יש לנסות מאוחר יותר'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
