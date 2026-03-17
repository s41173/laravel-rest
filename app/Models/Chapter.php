<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chapter extends BaseModel
{
    // nama table asli di database
    protected $table = 'club';
    const UPDATED_AT = 'updated';

    protected $fillable = [
        'code',
        'name',
    ];

    static function valid($input=0){
        $data = self::where('id', $input)->whereNull('deleted')->first();
        if (!$data){ return false; }else{ return $data; }
    }
}