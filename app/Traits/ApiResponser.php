<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

trait ApiResponser
{

    protected function successResponse($data, $code)
    {
        return response()->json($data, $code);
    }
    protected function errorResponse($message, $code)
    {
        return response()->json(['error' => $message, 'code' => $code], $code);
    }
    protected function showOne(Model $instance, $code)
    {
        return response()->json(['data' => $instance], $code);
    }
    protected function showAll(Collection $collection, $code)
    {
        return response()->json(['data' => $collection], $code);
    }
}
