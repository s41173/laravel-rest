<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\CustomerLoginStatus;
use App\Models\Chapter;
use App\Models\City;
use App\Models\District;
use App\Services\CustomerSessionService;
// use App\Services\NotifService;
use Illuminate\Support\Facades\Validator; // validator form
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

class CustomerController extends Controller
{
    protected $guard = 'api';
    protected $sessionService;

    public function __construct(CustomerSessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }

    function register(Request $request){
        $validator = Validator::make($request->all(), [
            'chapter'   => 'required|numeric',
            'name'      => 'required|string',
            'address'   => 'required|string',
            'zip'       => 'string',
            'phone'     => 'required|numeric',
            'email'     => 'required|email',
            'city'      => 'required',
            'password'  => 'required|string|min:8',
            'dob'       => 'required',
            'nik'       => 'required',
            'cartype'   => 'required',
            'vehicleno' => 'required',
        ]);
    
        if ($validator->fails()) { return api_response($validator->errors(),400);}

        // cek valid chapter
        if (Chapter::valid($request->chapter) == false){ return api_response("Chapter Not Found",400); }

        if (City::cekTrans('id',$request->city) == false){ return api_response('City Not Found', 400); }

        if (Customer::get_by_username($request->phone) != false){ return api_response("Phone Registered",400); }
        if (Customer::get_by_username($request->email) != false){ return api_response("Email Registered",400); }

        Customer::create([
            'clubid'    => $request->chapter,
            'first_name'=> $request->name,
            'address'   => $request->address,
            'zip'       => $request->zip,
            'phone1'    => $request->phone,
            'email'     => $request->email,
            'city'      => $request->city,
            'password'  => $request->password,
            'member_no' => Customer::counterplus().split_space(waktuindo()),
            'dob'       => date('Y-m-d', strtotime($request->dob)),
            'nik'       => $request->nik,
            'police_no' => $request->vehicleno,
            'car_type'  => $request->cartype,
            'joined'    => now(),
        ]);

        return api_response("Row Created");
    }

    function change_password(Request $request){
        $session = $this->sessionService->get();
        // print_r($session['user_id']);
        $validator = Validator::make($request->all(), [
            'new_password' => 'required|string|min:8'
        ]);
    
        if ($validator->fails()) { return api_response($validator->errors(),400);}

        $user = Customer::find($session['user_id']);

        // cek apakah password baru sama dengan password lama
        if (Hash::check($request->new_password, $user->password)) {
            return api_response("New Password Can't Equal Old Password",400);
        }

        $user->password = $request->new_password;
        $user->save();

        return api_response("Password Changed");
    }

    function get(){
        $session = $this->sessionService->get();
        $user = Customer::find($session['user_id']);
        return api_response($user);
    }

    function update(Request $request){
        $session = $this->sessionService->get();
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string',
            'address'   => 'required|string',
            'zip'       => 'string',
            'city'      => 'required',
            'cartype'   => 'required',
            'vehicleno' => 'required',
        ]);
    
        if ($validator->fails()) { return api_response($validator->errors(),400);}

        // cek valid chapter
        if (City::cekTrans('id',$request->city) == false){ return api_response('City Not Found', 400); }

        $user = Customer::find($session['user_id']);
        $user->first_name = $request->name;
        $user->address = $request->address;
        $user->zip = $request->zip;
        $user->city = $request->city;
        $user->police_no = $request->vehicleno;
        $user->car_type = $request->cartype;

        $user->save();
        return api_response("Row Updated");
    }

    public function uploadImage(Request $request)
    {
        $session = $this->sessionService->get();
        
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpg,jpeg,png,gif|max:1024'
        ]);
        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
    
        $user = Customer::findOrFail($session['user_id']);
    
        if ($user->image && Storage::disk('public')->exists('customer/'.$user->image)) {
            Storage::disk('public')->delete('customer/'.$user->image);
        }
    
        $fileName = str_replace(' ', '_', $user->id.$user->first_name)
                    .'.'.$request->image->extension();
    
        $path = $request->image->storeAs('customer', $fileName, 'public');
    
        $user->update([
            'image' => basename($path)
        ]);

        // $this->cropImage($fileName);
    
        return response()->json([
            'success' => true,
            'message' => 'Image uploaded successfully',
            'data' => $user->image
        ]);
    }

    

public function cropImage($fileName)
{
    $filePath = storage_path('app/public/customer/'.$fileName);

    // Pastikan file ada
    if (!file_exists($filePath)) {
        throw new \Exception("File not found for cropping: ".$filePath);
    }

    // Buka file
    $image = Image::make($filePath);

    // Crop / resize
    $image->fit(300, 300);

    // Simpan kembali
    $image->save();
}

    function get_city(){
        return api_response(City::all());
    }

    public function get_district($cityid)
    {
        if (!is_numeric($cityid)) {
            return api_response("Invalid parameter", 400);
        }

        $districts = District::where('id_kabupaten', $cityid)->get();

        if ($districts->isEmpty()) {
            return api_response("Not Found", 404);
        }

        return api_response($districts);
    }

}