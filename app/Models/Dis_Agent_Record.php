<?php
/**
 * 代理商获取佣金记录
 */

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Dis_Agent_Record extends Model
{
	
	protected  $primaryKey = "Record_ID";
	protected  $table = "distribute_agent_rec";
	public $timestamps = false;

	protected $fillable = [
	    'Users_ID','Account_ID','Real_Name','Account_Mobile','Record_Money','Record_Type','area_id','Record_CreateTime',
        'Order_ID','Order_CreateTime','Products_Name','Products_Qty','Products_PriceX','area_Proxy_Reward'
    ];
	
	//记录所属分销商
	public function DisAccount()
    {
        return $this->belongsTo(Dis_Account::class, 'Account_ID', 'Account_ID');
    }



}