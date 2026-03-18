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

    function testing() {

        $redis = Redis::connection();
    
        try {
            echo "Redis PING: ".$redis->ping().PHP_EOL; // biasanya PONG
        } catch (\Exception $e) {
            echo "Cannot connect to Redis: ".$e->getMessage().PHP_EOL;
        }

        // Redis::rpush('my_queue', 'Pesan dari Laravel');
        Redis::rpush('my_queue', 'halo dari laravel saya nih : '.date("H:i:s"));
    }

    function xtesting(){
        // Redis::set('test123', 'hello');
    //    $notif = new NotifService();

        // Contoh manggil method send_notif
        // $result = $notif->send_notif(0, 415);

        // print_r($notif->send_notif(7,415, 'Test OTP', 'INi OTP Kamu oh'));
        // $user = Customer::find(415);
        // $user->password = '123456789';
        // $user->save();

        $redis = Redis::connection();
try {
    echo $redis->ping(); // harus balas PONG
} catch (\Exception $e) {
    echo "Cannot connect to Redis: ".$e->getMessage();
}

        $data = json_encode([
            'name' => 'Jay',
            'role' => 'Software Engineer'
        ]);
        
        // channel sama yang didengarkan worker standalone
        Redis::publish('ci_channel', $data);

    }

    // Login: email/password atau phone1/password
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username'     => 'required|string',
            'password' => 'required|string|min:8',
        ]);
    
        if ($validator->fails()) { return api_response($validator->errors(),400);}

        $credentialsEmail = [
            'email' => $request->username, 'password' => $request->password, 'status' => 1
        ];
         
        $credentialsPhone = [
            'phone1' => $request->username,'password' => $request->password,'status' => 1
        ];

        $guard = auth($this->guard);

        if ($token = $guard->attempt($credentialsEmail) 
            ?: $guard->attempt($credentialsPhone)) {

            $user = $guard->user();
            $ttl = $guard->factory()->getTTL() * 60;

            //decode payload
            $payload = JWTAuth::setToken($token)->getPayLoad();
            
            $chapter = Chapter::find($user->clubid);

            // session object yang akan disimpan ke redis
            $sessionData = [
                'user_id' => $payload->get('sub'),
                'code' => $user->quinos_id,
                'email' => $user->email,
                'name' => $user->first_name,
                'phone' => $user->phone1,
                'chapter' => $user->clubid,
                'chapter_code' => $chapter->code,
                'device' => $request->userAgent(),
                'token' => $token,
                'login_at' => now()->toDateTimeString(),
            ];

            $key = "customer_session:{$user->id}";

            // Overwrite otomatis jika ingin login ulang
            Redis::setex($key, $ttl, json_encode($sessionData));
             // ------------  batas set value ke redis -------------

            CustomerLoginStatus::updateOrCreate(
                ['userid' => $user->id], // kondisi pencarian
                [
                    'log'         => $token,
                    'device'      => $request->userAgent(),
                    'joined'      => now(),
                    'req_count'   => 0,
                    'req_created' => now(),
                ]
            );

            return response()->json([
                'access_token' => $token,
                'token_type'   => 'bearer',
                'expires_in'   => $guard->factory()->getTTL() * 60,
                // 'customer_id'  => $user->id,
            ]);

            // return $this->respondWithToken($token);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function otp(Request $request)
    {
        $notif = new NotifService();

        $customer = Customer::where('phone1', $request->only('phone'))
                    ->whereNull('deleted')
                    ->first();
        if (!$customer) {return api_response('User Not Found', 404); }

        $reqcount = (int) CustomerLoginStatus::where('userid', $customer->id)
                    ->whereDate('req_created', today())
                    ->value('req_count') ?? 0;

        if ($reqcount >= 5) {
            return api_response('Maximum Limit OTP Request', 400);
        }

        $otp = mt_rand(1000, 9999);
        CustomerLoginStatus::setOtp($customer->id,$otp);
        $sent = $notif->send_notif(7,$customer->id, 
                                  "OTP Req : ".waktuIndo(), "Kode Pin OTP Anda : ".$otp,
                                  "member-forgot");

        return $sent ? api_response('OTP Has Been Sent'): api_response('Failed Sent OTP Request', 400);
    }

    function forgot(Request $request){
        $validator = Validator::make($request->all(), [
            'username'     => 'required|string',
            'new_password' => 'required|string|min:8',
            'otp'          => 'required|numeric',
        ]);
    
        if ($validator->fails()) { return api_response($validator->errors(),400);}

        $valid = Customer::valid_username($request->only("username"));
        if ($valid == false){ return api_response('Username / Phone Not Found',404); }
        
        $user = Customer::get_by_username($request->only("username")); // pass dari db
        if ($user->status != 1){ return api_response('Inactive User Status',400); }

         // cek apakah password baru sama dengan password lama
        if (Hash::check($request->new_password, $user->password)) {
            return api_response("New Password Can't Equal Old Password",400);
        }

        // cek apakah otp di db == otp di input
        $log = CustomerLoginStatus::get_by_userid($user->id);
        if (intval($log['log']) !== intval($request->otp)){ return api_response("Invalid OTP",400); }

        $user->password = $request->new_password;
        $user->save();
        
        CustomerLoginStatus::set_null_otp($user->id);
        return api_response("Password Changed");
    }

    function verify(Request $request){
        $validator = Validator::make($request->all(), [
            'username'     => 'required|string',
            'otp'          => 'required|numeric',
        ]);
    
        if ($validator->fails()) { return api_response($validator->errors(),400);}
        $valid = Customer::valid_username($request->only("username"));
        if ($valid == false){ return api_response('Username / Phone Not Found',404); }
        
        $user = Customer::get_by_username($request->only("username")); // pass dari db
        if ($user->status == 1 || $user->verified == 1){ return api_response("Active User Can't Verified",400); }

        // cek apakah otp di db == otp di input
        $log = CustomerLoginStatus::get_by_userid($user->id);
        if (intval($log['log']) !== intval($request->otp)){ return api_response("Invalid OTP",400); }

        $user->verified = 1;
        $user->save();
        
        CustomerLoginStatus::set_null_otp($user->id);
        return api_response("Status Verified");
    }
    
    // Ambil data user yang login
    public function decode()
    {
        // $user = auth($this->guard)->user();
        $session = $this->sessionService->get();
        // print_r($session);
        return response()->json($session);
    }

    // Logout, invalidate token
    public function logout()
    {
        $guard = auth($this->guard);
        $user = $guard->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        CustomerLoginStatus::where('userid', $user->id)
            ->update([
                'log' => null,
                'device' => null,
                'joined' => null
            ]);

        // hapus redis session
        Redis::del("customer_session:{$user->id}");

        $guard->logout();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    // Refresh token
    public function refresh()
    {
        $newToken = auth($this->guard)->refresh();
        return $this->respondWithToken($newToken);
    }

    // Helper response token
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth($this->guard)->factory()->getTTL() * 60
        ]);
    }
}