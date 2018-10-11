<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $table = 'area';
    protected $primaryKey = 'area_id';
    public $timestamps = false;

    protected $fillable = [
        'area_name', 'area_parent_id', 'area_sort', 'area_deep', 'area_region', 'area_code', 'letter'
    ];


    /**
     *获取地区列表 ，华北，华东，华南 等
     *@param  Array $except 例外地区id
     *@return Array $region 地区列表
     */
    public function getRegionList($except = [])
    {
        $builder = $this->where('area_deep',1);
        if(!empty($except)){
            $builder = $builder->whereNotIn('area_id',$except);
        }
        $province_all_array = $builder->get()->toArray();

        foreach ($province_all_array as $a) {
            if ($a['area_deep'] == 1 && $a['area_region'])
                $region[$a['area_region']][] = $a['area_id'];
        }

        return $region;

    }
}
