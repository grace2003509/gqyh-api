<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermissionConfig extends Model
{
    protected $primaryKey = "Permission_ID";
    protected $table = "permission_config";
    public $timestamps = false;

    protected $fillable = [
        'Users_ID', 'Perm_Name', 'Perm_Picture', 'Perm_Url', 'Perm_Tyle', 'Perm_On', 'Is_Delete',
        'Create_Time', 'Perm_Field', 'Perm_index'
    ];
}
