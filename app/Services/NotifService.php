<?php

namespace App\Services;
use Illuminate\Support\Facades\Http;
use App\Models\Property;
use App\Models\Customer;
use App\Models\CustomerLoginStatus;

class NotifService
{
    protected $apikey;
    protected $baseurl;

    public function __construct(){
        $property = Property::first();
        $this->apikey = $property->notif_token;
        $this->baseurl = $property->notif_url;
    }

    public function send_notif($type=0,$custid=0,$subject="",$content="",$modul="none",$target=0){
       
        if ($target == 0){
           $customer = Customer::find($custid);
           $loginstatus = CustomerLoginStatus::where('userid', $custid)->first();
           
           $device = $loginstatus->device; 
           $custname = $customer->first_name.' - '.$customer->last_name;
           $email = $customer->email;
           $phone = $customer->phone1;
        }
        
        $res = false; 
        if ($type == 0){
            $res = $this->post_notif(0,$email,$custid,$custname,$subject,$content,$modul);
        }elseif ($type == 1){
            $res = $this->post_notif(1,$phone,$custid,$custname,$subject,$content,$modul);
        }elseif ($type == 2){
          $res1 = $this->post_notif(0,$email,$custid,$custname,$subject,$content,$modul);
          $res2 = $this->post_notif(1,$phone,$custid,$custname,$subject,$content,$modul);
          if ($res1 == true && $res2 == true){ $res = true; }
        }elseif ($type == 3){
          $res = $this->post_notif(3,$device,$custid,$custname,$subject,$content,$modul);
        }elseif ($type == 4){
          $res = $this->post_notif(3,$device,$custid,$custname,$subject,$content,$modul);
          $res1 = $this->post_notif(1,$phone,$cust,$custname,$subject,$content,$modul);
          if ($res == true && $res1 == true){ $res = true; }
          
        }elseif ($type == 5){
          $res = $this->post_notif(3,$device,$custid,$custname,$subject,$content,$modul);
          $res1 = $this->post_notif(0,$email,$custid,$custname,$subject,$content,$modul);
          if ($res == true && $res1 == true){ $res = true; }
          
        }elseif ($type == 6){
          $res1 = $this->post_notif(0,$email,$custid,$custname,$subject,$content,$modul);
          $res2 = $this->post_notif(1,$phone,$custid,$custname,$subject,$content,$modul);
          $res3 = $this->post_notif(3,$device,$custid,$custname,$subject,$content,$modul);
          if ($res1 == true && $res2 == true && $res3 == true){ $res = true; }
        }elseif ($type == 7){
          $res1 = $this->post_notif(7,$phone,$custid,$custname,$subject,$content,$modul);
          if ($res1 == true){ $res = true; }
        }elseif ($type == 8){
          $res1 = $this->post_notif(0,$email,$custid,$custname,$subject,$content,$modul);
          $res2 = $this->post_notif(7,$phone,$custid,$custname,$subject,$content,$modul);
          if ($res1 == true && $res2 == true){ $res = true; }
        }
        return $res;
    }

   private function post_notif($type, $sentto, $cust, $custname, $subject, $content, $modul) {
      $postData = [
          'type' => $type,
          'customer' => $cust,
          'custname' => $custname,
          'subject' => $subject,
          'sentto' => $sentto,
          'content' => $content,
          'modul' => $modul
      ];

      $postString = http_build_query($postData, '', '&');

      // Panggil requestNotif, pastikan type = true agar dapat [body, status]
      $req = $this->requestNotif('notif/add_notif', $postString, true, 'POST'); 

      return ($req[1] == 200) ? true : false;
   }

    private function requestNotif($controller = null, $param = [], $type = null, $method = 'POST')
    {
        $apikey = $this->apikey;
        $url = $this->baseurl.$controller;

        $response = Http::withHeaders([
            'Content-Type' => 'application/x-www-form-urlencoded',
            'X-Auth-Token' => $apikey
        ])->send($method, $url, [
            'body' => $param
        ]);

        if (!$type) {
            return $response->body();
        }

        return [
            $response->body(),
            $response->status()
        ];
    }
}