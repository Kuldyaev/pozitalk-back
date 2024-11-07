<?php 

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes\Response;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\JsonContent;


class AuthController2 extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }
    public function login(Request $request)
    {
        $credentials=$request->only(['phone']);
        if(! $token = auth('api')->attempt($credentials)) {
            return response()->json(['error'=>'Unauthorizated'], 401);
        };
        return $this->respondWithToken($token);
    }

    public function user(){
        return response()->json(auth('api')->user());
    }

    protected function respondWithToken($token){
        return response()->json([
            'access_tocken'=>$token,
            'type'=>'Bearer',
            'expires_in'=>\Config::get('jwt.ttl')*60
        ]);
    }
}