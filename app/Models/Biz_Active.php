<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Biz_Active extends Model
{
    protected $table = 'biz_active';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'Users_ID', 'Active_ID', 'Biz_ID', 'ListConfig', 'IndexConfig', 'Status', 'addtime'
    ];


    public function active()
    {
        return $this->belongsTo(Active::class, 'Active_ID', 'Active_ID');
    }

    public function biz()
    {
        return $this->belongsTo(Biz::class, 'Biz_ID', 'Biz_ID');
    }

}
