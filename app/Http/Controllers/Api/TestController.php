<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

class TestController extends Controller
{

    /**
     * @api {get} /test/:id  测试接口
     * @apiName 测试接口
     * @apiGroup test
     * @apiDescription 测试接口
     *
     * @apiHeader {String} [access-key] Users unique access-key.
     *
     * @apiParam {Number} id 用户ID
     *
     * @apiSuccess {String} firstname Firstname of the Admin.
     *
     * @apiExample {curl} Example usage:
     *     curl -i http://localhost:6001/test/4711
     *
     * @apiSampleRequest /test/:id
     *
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "error": "UserNotFound"
     *     }
     * @apiSuccessExample {json} Success-Response:
     *     {
     *       "status": "1"
     *     }
     */
    public function test(Request $request, $id)
    {
        $data = ['status' => 1, 'msg' => 'ssss'];
        return json_encode($data);
    }
}
