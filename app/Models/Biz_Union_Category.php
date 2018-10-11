<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Biz_Union_Category extends Model
{
    protected $table = 'biz_union_category';
    protected $primaryKey = 'Category_ID';
    public $timestamps = false;

    protected $fillable = [
        'Users_ID', 'Category_Index', 'Category_Name', 'Category_ParentID', 'Category_ListTypeID',
        'Category_Img', 'Category_IndexShow'
    ];
}
