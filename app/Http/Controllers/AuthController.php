<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

use Exception;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

use Illuminate\Support\Facades\Log;

use Laravel\Passport\Token;

class AuthController extends Controller
{


    /**
     * @OA\Post(
     *      path="/api/login",
     *      tags={"Authentication"},
     *      summary="Login user",
     *      description="Logs in a user and generates an access token.",
     *      @OA\RequestBody(
     *          required=true,
     *          description="User credentials",
     *          @OA\JsonContent(
     *              required={"personal_number"},
     *              @OA\Property(property="personal_number", type="string", example="m7046317"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful login",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="name", type="string", example="Nethanel Efraim"),
     *              @OA\Property(property="permission_name", type="string", example="admin"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="message", type="string", example="המשתמש כבר מחובר."),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal server error",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="message", type="string", example="אירעה שגיאה בעת ההתחברות."),
     *          ),
     *      ),
     * )
     */



    //////
    public function login(Request $request)
    {



        try {

            if (auth()->check()) {
                return response()->json(['success' => false, 'message' => 'המשתמש כבר מחובר.'], 403);
            }

            $pn = $request->personal_number;
            $user = User::with('permission')
                ->where('personal_number', $pn)
                ->where('is_deleted', false)
                ->first();

            if (is_null($user)) {
                return response()->json(['success' => false, 'message' => 'המשתמש לא קיים במערכת, יש לפנות למסגרת אמ"ת.'], 403);
            }

            // Revoke last token
            $user->tokens()->delete();

            // Create token for user
            $token = $user->createToken("MashaToken");

            return response()->json([
                'success' => true,
                'name' => $user->name,
                'permission_name' => optional($user->permission)->permission_name,
            ], 201)->withCookie(
                Cookie::make('MashaToken', $token->accessToken)
            );
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['success' => false, 'message' => 'אירעה שגיאה בעת ההתחברות.'], 500);
        }
    }

    /**
     * @OA\Post(
     *      path="/api/logout",
     *      tags={"Authentication"},
     *      summary="Logout user",
     *      description="Logs out the currently authenticated user by revoking the access token.",
     *      security={{ "api_token": {} }},
     *      @OA\Response(
     *          response=201,
     *          description="Successful logout",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="ההתנתקות בוצעה בהצלחה."),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal server error",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="message", type="string", example="אירעה שגיאה בעת ההתנתקות."),
     *          ),
     *      ),
     * )
     */

    public function logout()
    {

        try {

            ///'delete' token
            Token::where('user_id', Auth::id())->update(['revoked' => true]);

            return response()->json([
                'success' => true,
                'message' => 'ההתנתקות בוצעה בהצלחה.',
            ], 201);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['success' => false, 'message' => 'אירעה שגיאה בעת ההתחברות.'], 500);
        }
    }


    /**
     * @OA\Get(
     *      path="/api/user",
     *      tags={"Authentication"},
     *      summary="Get current user",
     *      description="Retrieves information about the currently authenticated user.",
     *      security={{ "api_token": {} }},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="user", type="object",
     *                  @OA\Property(property="id", type="integer", example=11),
     *                  @OA\Property(property="name", type="string", example="Nethanel Efraim"),
     *                  @OA\Property(property="personal_number", type="string", example="s9810738"),
     *                  @OA\Property(property="phone_number", type="string", example="0532157802"),
     *                  @OA\Property(property="email", type="string", example="s9810738@army.idf.il"),
     *                  @OA\Property(property="employee_type", type="integer", example=1),
     *                  @OA\Property(property="permission", type="object",
     *                      @OA\Property(property="id", type="integer", example=1),
     *                      @OA\Property(property="code_permission", type="integer", example=1),
     *                      @OA\Property(property="permission_name", type="string", example="admin"),
     *                  ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal server error",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="message", type="string", example="אירעה שגיאה בעת ההתחברות."),
     *          ),
     *      ),
     * )
     */


    public function getCurrentUser()
    {
        try {

            $user = auth()->user();
            if (is_null($user)) {
                return response()->json(['success' => false, 'message' => 'המשתמש לא קיים במערכת, יש לפנות למסגרת אמ"ת.'], 403);
            }
            ///associated permission.
            $user->permission = $user->permission ? $user->permission->permission_name : null;
            return response()->json([
                'success' => true,
                'user' => $user
            ], 200);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['success' => false, 'message' => 'אירעה שגיאה בעת ההתחברות.'], 500);
        }
    }
}
