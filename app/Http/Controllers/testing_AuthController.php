<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\CustomerLoginStatus;
use App\Models\Chapter;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Redis;
use App\Services\CustomerSessionService;
use App\Services\NotifService;
use Illuminate\Support\Facades\Validator; // validator form
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    protected $guard = 'api';
    protected $sessionService;

    public function __construct(CustomerSessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }

    function testing(){
        $user = Customer::find(415);
    
        // langsung plain password, mutator otomatis hash
        $user->password = '12345678';
        $user->save();
    
        // cek hasil
        dd([
            'password_in_db' => $user->password,
            'check' => Hash::check('12345678', $user->password), // harus true
        ]);
    }

    // Login: email/password atau phone1/password
    public function login(Request $request)
    {
        $credentialsEmail = array_merge(
            $request->only('email', 'password'),
            ['status' => 1]
        );

        $credentialsPhone = array_merge(
            $request->only('phone1', 'password'),
            ['status' => 1]
        );

        $guard = auth($this->guard);

        if ($token = $guard->attempt($credentialsEmail) 
            ?: $guard->attempt($credentialsPhone)) {

            $user = $guard->user();
            $ttl = $guard->factory()->getTTL() * 60;

            //decode payload
            $payload = JWTAuth::setToken($token)->getPayLoad();


            return response()->json([
                'access_token' => $token,
                'token_type'   => 'bearer',
                'expires_in'   => $guard->factory()->getTTL() * 60,
                'customer_id'  => $user->id,
            ]);

            return $this->respondWithToken($token);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }
}