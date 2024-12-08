<?php

namespace App\Http\Controllers;

use App\Enums\CodePermission;
use App\Http\Requests\StoreUserRequest;
use App\Services\User\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Enums\Status;

class UserController extends Controller
{

    const KEVA = 1;
    const MILUIM = 2;
    const SADIR = 3;
    const OVED_TZAHAL = 4;



    protected $_userService;

    public function __construct()
    {
        $this->_userService = new UserService();
    }

    /**
     * @OA\Get(
     *      path="/api/users/",
     *      tags={"Users"},
     *      summary="Get all users",
     *      description="Retrieves all users from the system.",
     *      @OA\Response(
     *          response=200,
     *          description="Success response",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(
     *                  type="object",
     *                  @OA\Property(property="id", type="integer"),
     *                  @OA\Property(property="name", type="string"),
     *                  @OA\Property(property="personal_number", type="string"),
     *                  @OA\Property(property="phone_number", type="string"),
     *                  @OA\Property(property="email", type="string"),
     *                  @OA\Property(property="employee_type", type="integer"),
     *                  @OA\Property(property="permission", type="object",
     *                      @OA\Property(property="id", type="integer"),
     *                      @OA\Property(property="permission_name", type="string"),
     *                  ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="לא נמצאו משתמשים במערכת."),
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


    public function index()
    {

        $result = $this->_userService->getAllUsers();

        // Use match to handle different status cases
        return match ($result['status']) {

            Status::OK => response()->json(['data' => $result['data']], Response::HTTP_OK),

            Status::NOT_FOUND => response()->json(['message' => 'לא נמצאו משתמשים במערכת.'], Response::HTTP_NOT_FOUND),

            Status::INTERNAL_SERVER_ERROR => response()->json(['message' => 'התרחש בעיית שרתת יש לנסות שוב מאוחר יותר.'], Response::HTTP_INTERNAL_SERVER_ERROR),

            default => response()->json(['message' => 'Unknown error occurred.'], Response::HTTP_INTERNAL_SERVER_ERROR),
        };
    }

    /**
     * @OA\Get(
     *      path="/api/users/{search_string}",
     *      operationId="searchUser",
     *      tags={"Users"},
     *      summary="Search for users by personal number or full name",
     *      description="Searches for users in the system based on the provided personal number or full name.",
     *      @OA\Parameter(
     *          name="search_string",
     *          in="path",
     *          required=true,
     *          description="Search string (personal number or full name)",
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success response",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="id", type="integer"),
     *                  @OA\Property(property="name", type="string"),
     *                  @OA\Property(property="personal_number", type="string"),
     *                  @OA\Property(property="phone_number", type="string"),
     *                  @OA\Property(property="email", type="string"),
     *                  @OA\Property(property="employee_type", type="integer"),
     *                  @OA\Property(property="permission", type="object",
     *                      @OA\Property(property="id", type="integer"),
     *                      @OA\Property(property="code_permission", type="integer"),
     *                      @OA\Property(property="permission_name", type="string"),
     *                  ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="No content",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="חובה לשלוח מחרוזת לחיפוש.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="לא נמצאו משתמשים במערכת.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal server error",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="התרחשה בעיה בשרת. אנא נסה שוב מאוחר יותר.")
     *          )
     *      ),
     * )
     */


    public function searchUser(string $search_string)
    {


        // //trime spaces.
        $searchString = trim($search_string);
        if (strlen(trim($search_string))) {
            response()->json(['message' => 'חובה לשלוח מחרוזת לחפוש.'], Response::HTTP_NO_CONTENT);
        }

        $result = $this->_userService->searchUser($searchString);

        // Use match to handle different status cases
        return match ($result['status']) {

            Status::OK => response()->json(['data' => $result['data']], Response::HTTP_OK),

            Status::NOT_FOUND => response()->json(['message' => 'לא נמצאו משתמשים במערכת.'], Response::HTTP_NOT_FOUND),

            Status::INTERNAL_SERVER_ERROR => response()->json(['message' => 'התרחש בעיית שרתת יש לנסות שוב מאוחר יותר.'], Response::HTTP_INTERNAL_SERVER_ERROR),

            default => response()->json(['message' => 'Unknown error occurred.'], Response::HTTP_INTERNAL_SERVER_ERROR),
        };
    }

    /**
     * @OA\Post(
     *      path="/api/users",
     *      operationId="storeUser",
     *      tags={"Users"},
     *      summary="Create a new user",
     *      description="Creates a new user in the system.",
     *      @OA\RequestBody(
     *          required=true,
     *          description="User data",
     *          @OA\JsonContent(
     *              required={"name", "personal_number", "phone_number", "employee_type", "permission_code"},
     *              @OA\Property(property="name", type="string"),
     *              @OA\Property(property="personal_number", type="string", format="numeric", pattern="^\d{7}$"),
     *              @OA\Property(property="phone_number", type="string", format="phone", pattern="^05[0-9]{8}$"),
     *              @OA\Property(property="employee_type", type="integer", format="int32", minimum=1, maximum=4),
     *              @OA\Property(property="permission_code", type="string", description="Permission code from the permissions table"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success response",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="המשתמש התווסף בהצלחה.")
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
     *              @OA\Property(property="message", type="string", example="משתמש קיים במערכת.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal server error",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="התרחשה בעיה בשרת. אנא נסה שוב מאוחר יותר.")
     *          )
     *      ),
     * )
     */

    public function store(StoreUserRequest $request)
    {


        $result = $this->_userService->createUser($request);

        // Use match to handle different status cases
        return match ($result['status']) {

            Status::OK => response()->json(['message' => 'המשתמש התווסף בהצלחה.'], Response::HTTP_OK),

            Status::FORBIDDEN => response()->json(['message' => 'המשתמש אינו מורשה לבצע פעולה זו.'], Response::HTTP_FORBIDDEN),

            Status::CONFLICT => response()->json(['message' => 'משתמש קיים במערכת.'], Response::HTTP_CONFLICT),

            Status::INTERNAL_SERVER_ERROR => response()->json(['message' => 'התרחש בעיית שרתת יש לנסות שוב מאוחר יותר.'], Response::HTTP_INTERNAL_SERVER_ERROR),

            default => response()->json(['message' => 'Unknown error occurred.'], Response::HTTP_INTERNAL_SERVER_ERROR),
        };
    }


    /**
     * @OA\Put(
     *      path="/api/users/{id}",
     *      tags={"Users"},
     *      summary="Set new permission for a user",
     *      description="Sets a new permission for a user identified by the provided ID.",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the user to set the new permission for",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          description="Permission data",
     *          @OA\JsonContent(
     *              required={"code_permission"},
     *              @OA\Property(property="code_permission", type="integer", description="New permission code (1 indicate admin, 2 indicate user,3 indicate client)")
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success response",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="הרשאות משתמש עודכנו בהצלחה.")
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
     *          response=400,
     *          description="Bad request",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="משתמש אינו קיים במערכת.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal server error",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="התרחשה בעיה בשרת. אנא נסה שוב מאוחר יותר.")
     *          )
     *      ),
     * )
     */


    public function setNewPermission(Request $request, $id = null)
    {

        if (!$id) {
            return response()->json(['message' => 'חובה לשלוח מספר מזהה של הבקשה.'], Response::HTTP_NO_CONTENT);
        }
        if (!$request->code_permission) {
            return response()->json(['message' => 'חובה לשלוח קוד הרשאה של משתמש..'], Response::HTTP_NO_CONTENT);
        }

        // Validate set_color
        if (!in_array($request->code_permission, [CodePermission::ADMIN->value, CodePermission::USER->value, CodePermission::CLIENT->value])) {
            return response()->json(['message' => 'יש לשלוח קוד הרשאה תקין'], Response::HTTP_BAD_REQUEST);
        }


        $result = $this->_userService->setNewPermission($request, $id);

        // Use match to handle different status cases
        return match ($result['status']) {

            Status::OK => response()->json(['message' => 'הרשאות משתמש עודכנו בהצלחה.'], Response::HTTP_OK),

            Status::FORBIDDEN => response()->json(['message' => 'המשתמש אינו מורשה לבצע פעולה זו.'], Response::HTTP_FORBIDDEN),

            Status::BAD_REQUEST => response()->json(['message' => 'משתמש אינו קיים במערכת.'], Response::HTTP_CONFLICT),

            Status::INTERNAL_SERVER_ERROR => response()->json(['message' => 'התרחש בעיית שרתת יש לנסות שוב מאוחר יותר.'], Response::HTTP_INTERNAL_SERVER_ERROR),

            default => response()->json(['message' => 'Unknown error occurred.'], Response::HTTP_INTERNAL_SERVER_ERROR),
        };
    }

    /**
     * @OA\Delete(
     *      path="/api/users/{id}",
     *      tags={"Users"},
     *      summary="Delete a user by ID",
     *      description="Deletes a user from the system based on the provided ID.",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the user to delete",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success response",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="המשתמש הוסר בהצלחה.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="No content",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="חובה לשלוח מספר מזהה של הבקשה.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="משתמש אינו קיים במערכת.")
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

    public function delete($id = null)
    {
        if (!$id) {
            return response()->json(['message' => 'חובה לשלוח מספר מזהה של הבקשה.'], Response::HTTP_NO_CONTENT);
        }


        $result = $this->_userService->deleteUser($id);

        // Use match to handle different status cases
        return match ($result['status']) {

            Status::OK => response()->json(['message' => 'המשתמש הוסר בהצלחה.'], Response::HTTP_OK),

            Status::NOT_FOUND => response()->json(['message' => 'משתמש אינו קיים במערכת.'], Response::HTTP_NOT_FOUND),

            Status::FORBIDDEN => response()->json(['message' => 'המשתמש אינו מורשה לבצע פעולה זו.'], Response::HTTP_FORBIDDEN),

            Status::INTERNAL_SERVER_ERROR => response()->json(['message' => 'התרחש בעיית שרתת יש לנסות שוב מאוחר יותר.'], Response::HTTP_INTERNAL_SERVER_ERROR),

            default => response()->json(['message' => 'Unknown error occurred.'], Response::HTTP_INTERNAL_SERVER_ERROR),
        };
    }
}
