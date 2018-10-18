<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function test(Request $request, $id)
    {
        $data = ['status' => 1, 'msg' => 'ssss'];
        return json_encode($data);
    }
}
