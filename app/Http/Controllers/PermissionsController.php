<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionsController extends Controller
{

    /**
     * @OA\Get(
     *      path="/api/permissions",
     *      tags={"Permissions"},
     *      summary="Get all permissions",
     *      description="Retrieves all permissions available in the system.",
     *      @OA\Response(
     *          response=200,
     *          description="Success response",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(
     *                  type="object",
     *                  @OA\Property(property="id", type="integer", description="Permission ID"),
     *                  @OA\Property(property="code_permission", type="integer", description="Permission code"),
     *                  @OA\Property(property="permission_name", type="string", description="Permission name"),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal server error",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="בעיה בשרת. יש לנסות שוב מאוחר יותר.")
     *          )
     *      )
     * )
     */



    public function index()
    {
        try {

            $permissions = Permission::all();


            return response()->json(['data' => $permissions], Response::HTTP_OK);
        } catch (\Exception $e) {
            logger()->error($e->getMessage());
        }
        return response()->json(['message' => 'בעיה בשרת. יש לנסות שוב מאוחר יותר.'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }


    /**
     * @OA\Post(
     *      path="/api/permissions",
     *      tags={"Permissions"},
     *      summary="Create a new permission",
     *      description="Creates a new permission in the system.",
     *      @OA\RequestBody(
     *          required=true,
     *          description="Permission details",
     *          @OA\JsonContent(
     *              required={"permission_code", "permission_name"},
     *              @OA\Property(property="permission_code", type="integer", example=1, description="Unique code for the permission"),
     *              @OA\Property(property="permission_name", type="string", example="admin", description="Name of the permission"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Permission created successfully",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="הרשאה נוצרה בהצלחה.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad request",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="קוד הרשאה כבר קיים במערכת או שם הרשאה כבר קיים במערכת.")
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
     *              @OA\Property(property="message", type="string", example="התרחש בעיית שרת.יש לנסות שוב מאוחר יותר.")
     *          )
     *      )
     * )
     */


    public function store(Request $request)
    {
        try {

            $user = Auth::user();
            // only admin with role with permission_name
            if (optional($user->permission)->permission_name !== 'admin') {
                return response()->json(['message' => 'המשתמש אינו מורשה לבצע פעולה זו.'], Response::HTTP_FORBIDDEN);
            }



            // Validate the incoming request data
            $request->validate([
                'permission_code' => ['required', 'integer'],
                'permission_name' => ['required', 'string'],
            ]);

            if (Permission::where('code_permission', $request->permission_code)->where('is_deleted', 0)->first()) {
                return response()->json(['message' => 'קוד הרשאה כבר קיים במערכת.'], Response::HTTP_CONFLICT);
            }

            if (Permission::where('permission_name', $request->permission_name)->where('is_deleted', 0)->first()) {
                return response()->json(['message' => 'שם הרשאה כבר קיים במערכת.'], Response::HTTP_CONFLICT);
            }


            Permission::create([
                'code_permission' => $request->permission_code,
                'permission_name' => $request->permission_name,
            ]);

            return response()->json(['message' => 'הרשאה נוצרה בהצלחה.'], Response::HTTP_CREATED);
        } catch (\Exception $e) {

            logger()->error($e);
        }
        return response()->json(['message' => 'התרחש בעיית שרת.יש לנסות שוב מאוחר יותר.'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
