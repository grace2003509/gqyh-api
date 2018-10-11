<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Biz_Apply extends Model
{
    protected $table = 'biz_apply';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = ['Users_ID', 'Biz_ID', 'authtype', 'baseinfo', 'authinfo', 'accountinfo', 'payinfo',
        'CreateTime', 'Invitation_Code', 'status', 'is_del'];

    /**
     * 所属商家
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function biz()
    {
        return $this->belongsTo(Biz::class, 'Biz_ID', 'Biz_ID');
    }

}
