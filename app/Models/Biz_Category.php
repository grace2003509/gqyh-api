<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Biz_Category extends Model
{
    protected $table = 'biz_category';
    protected $primaryKey = 'Category_ID';
    public $timestamps = false;

    protected $fillable = [
        'Users_ID', 'Category_Index', 'Category_Name', 'Category_ParentID', 'Biz_ID'
    ];
}
