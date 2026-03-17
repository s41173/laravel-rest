<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    public static function counterplus($type = 0)
    {
        $maxId = static::max('id') ?? 0;

        return $type == 0
            ? $maxId + 1
            : $maxId;
    }

    public static function getById($id, $field = null)
    {
        if ($field !== null) {
            return static::where('id', $id)->value($field);
        }

        return static::find($id);
    }

    public static function cekTrans($field, $val)
    {
        return static::where($field, $val)->exists();
    }
}