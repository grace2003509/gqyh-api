<?php
/**
 * 分销账户Model
 */

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dis_Record extends Model
{
	use SoftDeletes;
	protected $dates = ['deleted_at'];
	
	protected  $primaryKey = "Record_ID";
    protected  $table = "distribute_record";
	public $timestamps = false;

	protected $fillable = [
	    'Users_ID','Buyer_ID','Owner_ID','Order_ID','Product_ID','Product_Price','Qty','Bonous_1','Bonous_2','Bonous_3',
        'status','Record_CreateTime','deleted_at','Biz_ID'
    ];
	

	//一条分销记录属于一个订单
	public function UserOrder(){
		return $this->belongsTo('UserOrder','Order_ID','Order_ID');
	}
	
	//一条分销记录对应一个购买者
	public function Buyer(){
		return $this->belongsTo('Member','Buyer_ID');
	}
	
	//一个分销记录对应一个拥有者
	public function Owner(){
		return $this->belongsTo('Member','Owner_ID');
	}
	
	
	//一条分销记录对应一个产品
	public function Product(){
		return $this->belongsTo('Product','Product_ID');
	}
	
	/*一条分销记录拥有多个分销佣金记录*/	
    public function DisAccountRecord(){
        return $this->hasMany('Dis_Account_Record','Ds_Record_ID','Record_ID');
    }


    /**
     *删除分销记录
     */
    public function delete_distribute_record($OrderID)
    {
        //删除分销记录
        $record_list = $this->select('Record_ID')->where('Order_ID', $OrderID)->get();
        $this->where('Order_ID', $OrderID)->delete();
        //删除分销账户记录
        if(count($record_list)>0){
            $dar_obj = new Dis_Account_Record();
            $dar_obj->whereIn('Ds_Record_ID', $record_list)->delete();
        }

        $dpr_obj = new Dis_Point_Record();
        $dpr_obj->where('orderid', $OrderID)->delete();

    }
	
	
}