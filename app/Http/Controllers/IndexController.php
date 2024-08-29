<?php

namespace App\Http\Controllers;

use App\Services\GsmTaskApiService;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function __invoke()
    {
        return view('index');
    }
}
