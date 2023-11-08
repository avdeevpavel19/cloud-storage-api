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

    public function destroy($message, $status = 'success', $code = 200)
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

    public function message($message, $status = 'success', $code = 200)
    {
        return response()->json(['message' => $message, 'status' => $status])->setStatusCode($code);
    }

    public function displayList($data, $nextPage = '?page=2')
    {
        return response()->json(['data' => $data, 'next_page' => $nextPage])->setStatusCode(200);
    }

    public function info(string $message)
    {
        return response()->json(['message' => $message])->setStatusCode(200);
    }
}
