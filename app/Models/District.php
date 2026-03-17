<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends BaseModel
{
    protected $table = 'kecamatan';

    public $timestamps = false;

    protected $fillable = [];

}
