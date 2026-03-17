<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;
use Tymon\JWTAuth\Facades\JWTAuth;

class CustomerSessionService
{
    protected $userid;

    protected function getUserId()
    {
        $payload = JWTAuth::parseToken()->getPayload();
        return $payload->get('sub');
    }

    protected function key($userId)
    {
        return "customer_session:{$userId}";
    }

    public function get()
    {
        $userId = $this->getUserId();
        $data = Redis::get($this->key($userId));

        return $data ? json_decode($data, true) : null;
    }

    public function destroy()
    {
        $userId = $this->getUserId();
        return Redis::del($this->key($userId));
    }
}