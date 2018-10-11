<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AreaRegion extends Model
{
    protected $table = 'area_region';
    protected $primaryKey = 'Region_ID';
    public $timestamps = false;

    protected $fillable = [
        'Users_ID', 'Area_ID', 'Region_Name', 'Region_ParentID', 'Region_Index', 'Region_Model'
    ];


    public function get_areainfo($areaid)
    {
        $a_obj = new Area();
        $r = $a_obj->where('area_id', $areaid)->first();
        return $r ? $r : false;
    }


    public function get_areaparent($areaid)
    {
        $data = array(
            "province"=>0
        );
        $r = $this->get_areainfo($areaid);
        if($r["area_deep"]==1){
            $data["province"] = $areaid;
        }elseif($r["area_deep"]==2){
            $data["city"] = $areaid;
            $data["province"] = $r["area_parent_id"];
        }elseif($r["area_deep"]==3){
            $parent = $this->get_areainfo($r["area_parent_id"]);
            $data["area"] = $areaid;
            $data["city"] = $r["area_parent_id"];
            $data["province"] = $parent["area_parent_id"];
        }
        return $data;
    }

    public function get_regionids($regionid)
    {
        $r = $this->find($regionid);
        $data = array(0,0);
        if($r){
            $data = array($r["Region_ID"],$r["Region_ParentID"]);
        }
        return $data;
    }

}
