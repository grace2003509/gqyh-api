<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Web_Article extends Model
{
    protected  $primaryKey = "Article_ID";
    protected  $table = "web_article";
    public $timestamps = false;

    protected $fillable = ['Users_ID','Article_Index','Article_Title','Column_ID','Article_ImgPath',
        'Article_Link','Article_LinkUrl', 'Article_BriefDescription','Article_Description','Article_CreateTime'];

    //隶属栏目
    public function column()
    {
        return $this->belongsTo(Web_Column::class, 'Column_ID', 'Column_ID');
    }

}
