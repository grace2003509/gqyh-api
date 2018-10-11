<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Web_Column extends Model
{
    protected  $primaryKey = "Column_ID";
    protected  $table = "web_column";
    public $timestamps = false;

    protected $fillable = ['Users_ID','Column_Name','Column_ImgPath','Column_Link','Column_LinkUrl',
        'Column_PopSubMenu','Column_NavDisplay', 'Column_ListTypeID','Column_Index','Column_Description',
        'Column_ParentID','Column_PageType','Column_ChildTypeID'];
}
