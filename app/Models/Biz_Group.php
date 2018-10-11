<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Biz_Group extends Model
{
    protected $primaryKey = "Group_ID";
    protected $table = "biz_group";
    public $timestamps = false;

    protected $fillable = ['Users_ID', 'Group_Name', 'Group_Index', 'Group_IsStore', 'is_default'];
}
