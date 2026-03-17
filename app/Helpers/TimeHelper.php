<?php

use Carbon\Carbon;

if (!function_exists('waktuIndo')) {
    function waktuIndo()
    {
        return Carbon::now('Asia/Jakarta')->format('H:i:s');
    }
}