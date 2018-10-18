<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UploadFile extends Model
{
    protected  $primaryKey = "UploadFiles_ID";
    protected  $table = "uploadfiles";
    public $timestamps = false;

    protected $fillable = [
        'UploadFiles_ID', 'UploadFiles_IsUse', 'UploadFiles_TableField','UploadFiles_DirName','UploadFiles_SavePath',
        'UploadFiles_FileName','UploadFiles_FileSize','UploadFiles_CreateDate',
    ];
}
