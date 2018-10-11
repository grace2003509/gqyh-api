<?php
/**
 * Created by PhpStorm.
 * Admin: apple
 * Date: 2016/10/7
 * Time: 23:36
 */

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use SplSubject;
use SplObserver;
use SplObjectStorage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;



class Service implements ServiceInterface, SplSubject
{
    //观察者集合
    private $observers = NULL;

    //是否使用缓存
    public  $useCache  = false;

    //缓存时间:天数,默认缓存30天,实时性要求比较高的,请设置更小的数值或者取消缓存
    public  $expires   = 30;

    //默认分页条数
    public  $pageSzie  = 20;

    //错误响应
    public  $errors    = array(
        'errorCode' => 0,
        'errorMsg'  => '',
        'data'      => []
    );

    //当前观察者
    public $currentObserver;

    //当前实例
    public $currentSubject;

    //当前实例名称
    public $instanceName;

    //当前方法名称
    public $methodName;


    public function  __construct()
    {
        $this->observers       = new SplObjectStorage();
        $this->currentObserver = new ServicesObserver($this);
        $this->attach($this->currentObserver);
    }


    /**
     * @param SplObserver $observer
     */
    public function attach (SplObserver $observer){
        $this->observers->attach($observer);
    }


    /**
     * @param SplObserver $observer
     */
    public function detach (SplObserver $observer){
        $this->observers->detach($observer);
    }


    /**
     *
     * @return mixed
     */
    public function notify (){
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }


    /**
     * @param null $id
     * @return mixed
     */
    public function getOne($id = null){

    }


    /**
     * @param $where
     * @return mixed
     */
    public function getFirst(Collection $where = null){

    }


    /**
     * @param Collection|null $where
     * @param null $order
     * @param int $page
     * @param int $size
     */
    public function getList(Collection $where=null, $order=null, $page=1, $size=20){

    }


    /**
     * @return mixed
     */
    public function getAll(){

    }


    /**
     * @param $data
     * @return mixed
     */
    public function create(Collection $data){

    }


    /**
     * @param $where
     * @param $data
     * @return mixed
     */
    public function update(Collection $where, Collection $data){

    }


    /**
     * @param Collection|null $where
     * @param Collection|null $primaryKey
     * @return mixed
     */
    public function delete(Collection $where = null, Collection $primaryKey=null){

    }


    /**
     * @return mixed
     */
    public function export(){

    }

    /**
     * @param Collection $data
     * @return mixed
     */
    public function import(Collection $data){

    }


    /**
     * @return mixed
     */
    public function view(){

    }


    /**
     * @return mixed
     */
    public function review(){

    }


    /**
     * @param array $data
     * @param array $rules
     * @param array $messages
     * @param $errors
     * @return bool
     */
    public function check(Array $data, Array $rules, Array $messages, $errors){

        $validator = Validator::make($data, $rules, $messages);

        if($validator->fails()){
            $errors = $validator->messages()->first();
            return false;
        }

        return true;
    }



    /**
     * @param $key
     * @param \Closure $closure
     * @return mixed
     */
    public function fromCache($key, \Closure $closure){

        if($this->useCache == true){
            $rs = Cache::has($key) ? Cache::get($key) : $closure();
            return $rs;
        }
        return $closure();
    }



    /**
     * @param $key
     * @param Collection $data
     * @return bool
     */
    public function toCache($key, $data){
        if($this->useCache == true){
            $expiresAt  = Carbon::now()->addDays($this->expires);
            $storeCache = Cache::add($key, $data, $expiresAt);
            if(!$storeCache){
                Log::error("cache $key failed");
                return false;
            }
            return true;
        }
        return false;
    }


    /**
     * @param $errorCode
     * @param $errorMsg
     * @param array $data
     * @param $isJson
     * @return array|string
     */
    public function response($errorCode=0, $errorMsg='', $data=[], $isJson = false){
        $this->errors = array(
            'errorCode' => $errorCode,
            'errorMsg'  => $errorMsg,
            'data'      => empty($data) ? [] : $data
        );
        $rs = $isJson == true ? json_encode($this->errors) : $this->errors;
        return $rs;
    }



    /**
     * @param null $modleName
     * @param null $where
     * @param null $order
     * @return mixed
     */
    public function query($modleName=null, $where=null, $order=null){
        $index = 0;
        $modle = "App\\Models\\{$modleName}";

        $rs    = null;
        if(!empty($where)){
            foreach($where as $key=>$item){
                $key = str_replace('and_','',$key);
                //$counts = extract($item, EXTR_PREFIX_ALL,'arg');
                $rs = ($index <= 0) ? call_user_func_array([$modle, $key], $item) : call_user_func_array([$rs, $key], $item);
                $index++;
            }
        }

        //此处没有直接返回->get() 便于下一次检索或者update
        $rs = empty($order) ? $rs : call_user_func_array([$rs, 'orderBy'], $order);

        return $rs;
    }


    /**
     * 手动分页
     * @param null $current_page
     * @param null $datalist
     * @param null $total
     * @param int $perPage
     * @return mixed
     */
    public function page($datalist = null , $total = null,$perPage = 20 , $current_page = 1){
        $paginator =new LengthAwarePaginator($datalist, $total, $perPage, $current_page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => 'page',
        ]);
        //$pager['data'] = $paginator->toArray()['data'];
        return $paginator;
    }



}