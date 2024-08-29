<?php

namespace App\Http\Controllers;

use App\Services\GsmTaskApiService;
use Exception;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Http\Request;

class TrackApiController extends Controller
{
    /**
     * @throws HttpClientException
     */
    public function tasks(Request $request, GsmTaskApiService $service)
    {
        if ($request->has('order_number')) {
            return $service->getTaskByOrderNumber($request->get('order_number'));
        }

        return $service->getTasks();
    }

    /**
     * @throws Exception
     */
    public function taskEvents($taskId, GsmTaskApiService $service)
    {
        return $service->getTaskEvents($taskId);
    }

    /**
     * @throws Exception
     */
    public function taskEventsByOrder(Request $request, GsmTaskApiService $service)
    {
        if ($request->has('order')) {
            return $service->getTaskEventsByOrderNumber($request->get('order'));
        }

        return [
            'message' => "No order number specified."
        ];
    }
}
