<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class CustomerLoginStatus extends BaseModel
{
    protected $table = 'customer_login_status';

    public $timestamps = false;

    protected $fillable = [
        'userid',
        'log',
        'device',
        'joined',
        'req_count',
        'req_created',
    ];

    public static function setOtp($user = 0, $otp = 0)
    {
        $today = today();

        $row = self::where('userid', $user)
            ->whereDate('req_created', $today)
            ->first();

        if ($row) {
            $row->req_count = $row->req_count + 1;
        } else {
            $row = self::firstOrNew(['userid' => $user]);
            $row->req_count = 1;
        }

        $row->log = $otp;
        $row->req_created = now();
        
        return $row->save();
    }

    static function set_null_otp($user=0){
        $row = self::where('userid', $user)->first();
        $row->log = null;
        return $row->save();
    }

    public static function get_by_userid($userid=0)
    {
        $row = self::where('userid', $userid)->first();
        return $row ? $row->toArray() : null;
    }

}
