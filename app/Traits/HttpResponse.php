<?php

namespace App\Traits;

trait HttpResponse
{
    public function success($data, $status = 'success', $code = 200)
    {
        return response()->json(['data' => $data, 'status' => $status])->setStatusCode($code);
    }

    public function error($message, $status = 'error', $code = 500)
    {
        return response()->json(['message' => $message, 'status' => $status])->setStatusCode($code);
    }

    public function delete($message, $status = 'success', $code = 200)
    {
        return response()->json(['message' => $message, 'status' => $status])->setStatusCode($code);
    }

    public function notFound($message, $status = 'info', $code = 404)
    {
        return response()->json(['message' => $message, 'status' => $status])->setStatusCode($code);
    }

    public function created($data, $status = 'created', $code = 201)
    {
        return response()->json(['data' => $data, 'status' => $status])->setStatusCode($code);
    }
}
