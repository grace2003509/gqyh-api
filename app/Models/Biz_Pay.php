<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Biz_Pay extends Model
{
    protected $primaryKey = "id";
    protected $table = "biz_pay";
    public $timestamps = false;

    protected $fillable = ['Users_ID', 'biz_id', 'bond_free', 'years', 'year_free', 'total_money', 'status',
        'order_paymentmethod', 'type', 'addtime', 'paytime'];



    public function biz()
    {
        return $this->belongsTo(Biz::class, 'biz_id', 'Biz_ID');
    }

}
