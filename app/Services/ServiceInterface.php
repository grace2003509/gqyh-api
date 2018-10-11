<?php
/**
 * Created by PhpStorm.
 * Admin: apple
 * Date: 2016/10/7
 * Time: 23:40
 */

namespace App\Services;

use Illuminate\Support\Collection;

interface ServiceInterface
{

    /**
     * @param null $id
     * @return mixed
     */
    public function getOne($id=null);


    /**
     * @param $where
     * @return mixed
     */
    public function getFirst(Collection $where);


    /**
     * @param Collection|null $where
     * @param null $order
     * @param int $page
     * @param int $size
     * @return mixed
     */
    public function getList(Collection $where=null, $order=null, $page=1, $size=20);


    /**
     * @return mixed
     */
    public function getAll();


    /**
     * @param $data
     * @return mixed
     */
    public function create(Collection $data);


    /**
     * @param $where
     * @param $data
     * @return mixed
     */
    public function update(Collection $where, Collection $data);


    /**
     * @param Collection $where
     * @param Collection $primaryKey
     * @return mixed
     */
    public function delete(Collection $where, Collection $primaryKey);


    /**
     * @return mixed
     */
    public function export();

    /**
     * @param Collection $data
     * @return mixed
     */
    public function import(Collection $data);


    /**
     * @return mixed
     */
    public function view();


    /**
     * @return mixed
     */
    public function review();


    /**
     * @param $key
     * @param \Closure $closure
     * @return mixed
     */
    public function fromCache($key, \Closure $closure);



}