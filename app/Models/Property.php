<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    // nama table asli di database
    protected $table = 'property';
    public $timestamps = false;
}