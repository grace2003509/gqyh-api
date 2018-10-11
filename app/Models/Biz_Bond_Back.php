<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Biz_Bond_Back extends Model
{
    protected $table = 'biz_bond_back';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = ['Users_ID', 'biz_id', 'back_money', 'info', 'status', 'addtime',
        'alipay_account', 'alipay_username'];

    /**
     * 所属商家
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function biz()
    {
        return $this->belongsTo(Biz::class, 'biz_id', 'Biz_ID');
    }
}
