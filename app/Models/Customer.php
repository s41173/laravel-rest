<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Customer extends Authenticatable implements JWTSubject
{
    use Notifiable;

    // nama table asli di database
    protected $table = 'customer';
    const UPDATED_AT = 'updated';
    const CREATED_AT = 'created';

    protected $fillable = [
        'clubid',
        'first_name',
        'address',
        'zip',
        'phone1',
        'email',
        'city',
        'password',
        'dob',
        'nik',
        'police_no',
        'car_type',
        'joined',
        'member_no',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // JWT interface methods
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    // Optional: mutator untuk hash password (password otomatis dihash oleh function ini)
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = bcrypt($password);
    }

    static function valid_username($input){
        $phone = self::where('phone1', $input)->whereNull('deleted')->first();
        if ($phone){ return 'phone'; }

        $username = self::where('phone1', $input)->whereNull('deleted')->first();
        if ($username){ return 'username'; }

        return false;
    }

    public static function get_by_username($input)
    {
        $user = self::where(function ($query) use ($input) {
                        $query->where('phone1', $input)
                            ->orWhere('email', $input);
                    })
                    ->whereNull('deleted')
                    ->first();

        if (!$user) {return false;}
        return $user;
    }

    public static function counterplus($type = 0)
    {
        $maxId = self::max('id') ?? 0;

        return $type == 0
            ? $maxId + 1
            : $maxId;
    }

}