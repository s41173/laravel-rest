<?php

function api_response($message = '', $status = 200)
{
    return response()->json([
        'success' => $status >= 200 && $status < 300,
        'message' => $message
    ], $status);
}

function split_space($string)
{
    return strtolower(trim(preg_replace('~[^0-9a-z]+~i', '-', html_entity_decode(preg_replace('~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i', '$1', htmlentities($string, ENT_QUOTES, 'UTF-8')), ENT_QUOTES, 'UTF-8')), '-'));
}