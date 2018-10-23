<?php

namespace App\Http\Controllers\Api;

use App\Models\Dis_Account;
use App\Models\Dis_Level;
use App\Models\Member;
use App\Models\PermissionConfig;
use App\Models\ShopConfig;
use App\Models\UploadFile;
use App\Services\ImageThum;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class UserInfoController extends Controller
{
    /**
     * @api {get} /center/user_info  用户信息
     * @apiGroup 会员中心
     * @apiDescription 获取用户信息
     *
     * @apiHeader {String} access-key   用户登陆认证token
     *
     * @apiParam {Number}   UserID      用户ID
     *
     * @apiSuccess {Number} status      状态码（0:失败，1:成功, -1:需要重新登陆）
     * @apiSuccess {String} msg         返回状态说明信息
     * @apiSuccess {Object} data        用户信息数据
     *
     * @apiExample {curl} Example usage:
     *     curl -i http://localhost:6002/api/center/user_info
     *
     * @apiSampleRequest /api/center/user_info
     *
     * @apiErrorExample {json} Error-Response:
     *     {
     *          "status": "0",
     *          "msg": "失败",
     *     }
     * @apiSuccessExample {json} Success-Response:
     *     {
     *          "status": "1",
     *          "msg": "成功",
     *          "data": {
     *                  "User_ID": 17,  //用户ID
     *                  "User_No": 600017,  //用户编号
     *                  "User_Mobile": "13274507043",  //用户手机号
     *                  "User_NickName": null,  //用户昵称
     *                  "User_Integral": 20,  //用户当前积分
     *                  "User_Money": 1314,  //用户当前余额
     *                  "User_Level": 0,  //用户等级
     *                  "User_HeadImg": "http://localhost:6001//uploadfiles/9nj50igwex/image/5b87a19025.png",  //用户头像
     *                  "Is_Distribute": 0,  //是否是分销商（0:普通账户，1:分销账户）
     *                  "User_CreateTime": "2018-10-15 10:53:54",  //注册时间
     *              },
     *     }
     */
    public function user_info(Request $request)
    {
        $input = $request->input();

        $rules = [
            'UserID' => 'required|exists:user,User_ID'
        ];
        $message = [
            'UserID.required' => '缺少必要的参数UserID',
            'UserID.exists' => '此用户不存在',
        ];
        $validator = Validator::make($input, $rules, $message);
        if($validator->fails()){
            $data = ['status' => 0, 'msg' => $validator->messages()->first()];
            return json_encode($data);
        }

        $m_obj = new Member();
        $sc_obj = new ShopConfig();
        $dl_obj = new Dis_Level();
        $dis_level = $dl_obj->select('Level_Name')->get();
        $dis_level = get_dropdown_list($dis_level, 'Level_ID', 'Level_Name');
        $shopconfig = $sc_obj->select('ShopLogo')->find(USERSID);

        $filter = ['User_ID', 'User_No', 'User_Mobile', 'User_NickName', 'User_Level', 'User_HeadImg', 'Is_Distribute',
            'User_CreateTime','User_Integral', 'User_Money'];
        $user = $m_obj->select($filter)->find($input['UserID']);
        if($user->Is_Distribute == 1){
            $user->User_Level = $dis_level[@$user->disAccount->Level_ID];
        }else{
            $user->User_Level = '普通用户';
        }
        if($user->User_HeadImg == ''){
            $user->User_HeadImg = ADMIN_BASE_HOST.$shopconfig->ShopLogo;
        }
        $user->User_CreateTime = date('Y-m-d H:i:s', $user->User_CreateTime);

        $data = [
            'status' => 1,
            'msg' => '获取用户信息成功',
            'data' => $user,
        ];
        return json_encode($data);
    }


    /**
     * @api {get} /center/menu_list  菜单列表
     * @apiGroup 会员中心
     * @apiDescription 获取会员中心菜单列表
     *
     * @apiHeader {String} access-key   用户登陆认证token
     *
     * @apiParam {Number}   UserID      用户ID
     *
     * @apiSuccess {Number} status      状态码（0:失败，1:成功, -1:需要重新登陆）
     * @apiSuccess {String} msg         返回状态说明信息
     * @apiSuccess {Object} data        用户信息数据
     *
     * @apiExample {curl} Example usage:
     *     curl -i http://localhost:6002/api/center/menu_list
     *
     * @apiSampleRequest /api/center/menu_list
     *
     * @apiErrorExample {json} Error-Response:
     *     {
     *          "status": "0",
     *          "msg": "失败",
     *     }
     * @apiSuccessExample {json} Success-Response:
     *     {
     *          "status": "1",
     *          "msg": "成功",
     *          "data": [
     *              {
     *                  "Perm_Name": "设置",   //菜单名称
     *                  "Perm_Picture": "http://localhost:6001/uploadfiles/9nj50igwex/image/5b4042127f.png",  //图标
     *                  "Perm_Url": "/api/shop/member/setting/"   //路径
     *              },
     *              {
     *                  "Perm_Name": "退款/售后",
     *                  "Perm_Picture": "http://localhost:6001/uploadfiles/9nj50igwex/image/5b404231c2.png",
     *                  "Perm_Url": "/api/shop/member/backup/status/5/"
     *              },
     *          ]
     *     }
     */
    public function menu_list(Request $request)
    {
        $input = $request->input();

        $rules = [
            'UserID' => 'required|exists:user,User_ID'
        ];
        $validator = Validator::make($input, $rules);
        if($validator->fails()){
            $data = ['status' => 0, 'msg' => $validator->messages()->first()];
            return json_encode($data);
        }

        $pc_obj = new PermissionConfig();

        $perm_config = $pc_obj->select('Perm_Name', 'Perm_Picture', 'Perm_Url')
            ->where('Perm_Tyle', 2)
            ->where('Is_Delete', 0)
            ->orderByDesc('Perm_Index')
            ->get();
        foreach($perm_config as $key => $value){
            $value['Perm_Picture'] = ADMIN_BASE_HOST.$value['Perm_Picture'];
        }

        $data = [
            'status' => 1,
            'msg' => '获取菜单列表成功',
            'data' => $perm_config,
        ];
        return json_encode($data);
    }


    /**
     * @api {post} /center/upload_headimg  上传用户头像
     * @apiGroup 会员中心
     * @apiDescription 上传用户头像
     *
     * @apiHeader {String} access-key   用户登陆认证token
     *
     * @apiParam {Number}   UserID      用户ID
     * @apiParam {String}   up_head     上传图片元素名称
     *
     * @apiSuccess {Number} status      状态码（0:失败，1:成功, -1:需要重新登陆）
     * @apiSuccess {String} msg         返回状态说明信息
     *
     * @apiExample {curl} Example usage:
     *     curl -i http://localhost:6002/api/center/upload_headimg
     *
     * @apiSampleRequest /api/center/upload_headimg
     *
     * @apiErrorExample {json} Error-Response:
     *     {
     *          "status": "0",
     *          "msg": "失败",
     *     }
     * @apiSuccessExample {json} Success-Response:
     *     {
     *          "status": "1",
     *          "msg": "头像上传成功",
     *     }
     */
    public function upload_headimg(Request $request)
    {
        $input = $request->input();

        $rules = [
            'UserID' => 'required|exists:user,User_ID',
            'up_head' => 'required'
        ];
        $message = [
            'UserID.required' => '缺少必要的参数UserID',
            'UserID.exists' => '此用户不存在',
            'up_head.required' => '请选择图片上传',
        ];
        $validator = Validator::make($input, $rules, $message);
        if($validator->fails()){
            $data = ['status' => 0, 'msg' => $validator->messages()->first()];
            return json_encode($data);
        }

        $php_path = ADMIN_BASE_HOST.'/';
        $save_path = $php_path.'uploadfiles/userfile/';
        $save_url = '/uploadfiles/userfile/';
        $ext_arr = array(
            'image' => array('gif', 'jpg', 'jpeg', 'png'),
        );
        $max_size = 8 * 1024 * 1024;
        $save_path = realpath($save_path) . '/';

        if (!empty($_FILES['up_head']['error'])) {
            switch($_FILES['up_head']['error']){
                case '1':
                    $error = "上传文件大小超过" . ($max_size) . "K限制。";	//'超过php.ini允许的大小。';
                    break;
                case '2':
                    $error = '超过表单允许的大小。';
                    break;
                case '3':
                    $error = '图片只有部分被上传。';
                    break;
                case '4':
                    $error = '请选择图片。';
                    break;
                case '6':
                    $error = '找不到临时目录。';
                    break;
                case '7':
                    $error = '写文件到硬盘出错。';
                    break;
                case '8':
                    $error = 'File upload stopped by extension。';
                    break;
                case '999':
                default:
                    $error = '未知错误。';
            }
            $data = ['status' => 0, 'msg' => $error];
            return json_encode($data);
        }

        //有上传文件时
        if (empty($_FILES) === false) {
            //原文件名
            $file_name = $_FILES['up_head']['name'];
            //服务器上临时文件名
            $tmp_name = $_FILES['up_head']['tmp_name'];
            //文件大小
            $file_size = $_FILES['up_head']['size'];
            $error = '';
            //检查文件名
            if (!$file_name) {
                $error = '请选择文件';
            }
            //检查目录
            if (@is_dir($save_path) === false) {
                $error = '上传目录不存在';
            }
            //检查目录写权限
            if (@is_writable($save_path) === false) {
                $error = '上传目录没有写权限';
            }
            //检查是否已上传
            if (@is_uploaded_file($tmp_name) === false) {
                $error = '上传失败';
            }
            //检查文件大小
            if ($file_size > $max_size) {
                $error = "上传文件大小超过".($max_size/1024)."K限制。";
            }
            //检查目录名
            $dir_name = !isset($input['dir']) ? 'image' : trim($input['dir']);
            if (empty($ext_arr[$dir_name])) {
                $error = '目录名不正确';
            }

            if($error != ''){
                $data = ['status' => 0, 'msg' => $error];
                return json_encode($data);
            }

            //获得文件扩展名
            $temp_arr = explode(".", $file_name);
            $file_ext = array_pop($temp_arr);
            $file_ext = trim($file_ext);
            $file_ext = strtolower($file_ext);
            //检查扩展名
            if (in_array($file_ext, $ext_arr[$dir_name]) === false) {
                $error = "上传文件扩展名是不允许的扩展名。\n只允许" . implode(",", $ext_arr[$dir_name]) . "格式。";
                $data = ['status' => 0, 'msg' => $error];
                return json_encode($data);
            }
            //创建文件夹
            if (!file_exists($save_path)) {
                mkdir($save_path);
            }
            $save_path .= $input['UserID'] . "/";
            $save_url .= $input['UserID'] . "/";
            if (!file_exists($save_path)) {
                mkdir($save_path);
            }
            if ($dir_name !== '') {
                $save_path .= $dir_name . "/";
                $save_url .= $dir_name . "/";
                if (!file_exists($save_path)) {
                    mkdir($save_path);
                }
            }
            //新文件名
            $UploadFilesID=dechex(time()) . dechex(rand(16, 255));
            $new_file_name = $UploadFilesID . '.' . $file_ext;
            //移动文件
            $file_path = $save_path . $new_file_name;

            if($file_ext != 'jpeg'){
                $image = imagecreatefromstring(file_get_contents($tmp_name));
                $exif = exif_read_data ($tmp_name,0,true);
                if(isset($exif['IFD0']['Orientation'])) {
                    switch ($exif['IFD0']['Orientation']) {
                        case 8:
                            $image = imagerotate($image, 90, 0);
                            break;
                        case 3:
                            $image = imagerotate($image, 180, 0);
                            break;
                        case 6:
                            $image = imagerotate($image, -90, 0);
                            break;
                    }
                    imagejpeg($image, $tmp_name);
                }
            }

            if (move_uploaded_file($tmp_name, $file_path) === false) {
                $error = '上传文件失败';
                $data = ['status' => 0, 'msg' => $error];
                return json_encode($data);
            }

            @chmod($file_path, 0644);
            $file_url = $save_url . $new_file_name;
            if (in_array($file_ext, $ext_arr['image']) !== false) {
                //创建文件夹
                if (!file_exists($save_path . 'n0/')) {
                    mkdir($save_path . 'n0/');
                }
                $thumImg = new ImageThum();
                //开始缩略图
                $thumImg->littleImage($file_path, $save_path . 'n0/', 200);
            }

            /*向数据库中插入记录*/
            $uf_obj = new UploadFile();
            $files_data = [
                'UploadFiles_ID' => $UploadFilesID,
                'UploadFiles_TableField' => 'userfile',
                'UploadFiles_DirName' => $dir_name,
                'UploadFiles_SavePath' => $file_url,
                'UploadFiles_FileName' => $file_name,
                'UploadFiles_FileSize' => number_format($file_size/1024,2,".",""),
                'UploadFiles_CreateDate' => date("Y-m-d H:i:s"),
            ];
            $uf_obj->create($files_data);

            $m_obj = new Member();
            $da_obj = new Dis_Account();
            $Account = $da_obj->where('User_ID', $input['UserID'])->first();
            if ($Account) {
                $Account->Shop_Logo = $file_url;
                $Account->Is_Regeposter = 1;
                $Flag = $Account->save();
            }
            //同时用户表的头像信息
            $Flag = $m_obj->where('User_ID', $input['UserID'])->update(['User_HeadImg' => $file_url]);

            $data = ['status' => 1, 'msg' => '头像上传成功'];
            return json_encode($data);
        }
    }

}
