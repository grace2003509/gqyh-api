<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Biz extends Model
{
    protected $primaryKey = "Biz_ID";
    protected $table = "biz";
    public $timestamps = false;

    protected $fillable = [
        'Users_ID','Biz_Account','Biz_PassWord','Biz_Name','Area_ID','Biz_Province','Biz_City','Biz_Area',
        'Biz_Address','Biz_PrimaryLng','Biz_PrimaryLat','Biz_Contact','Biz_Phone','Biz_Email','Biz_Homepage',
        'Biz_Introduce','Biz_Status','Biz_CreateTime','Biz_SmsPhone','Shipping','Default_Shipping','Default_Business',
        'Biz_RecieveProvince','Biz_RecieveCity','Biz_RecieveArea','Biz_RecieveAddress','Biz_RecieveName',
        'Biz_RecieveMobile','Is_Union','Skin_ID','Biz_Logo','Biz_Qrcode','Biz_Kfcode','Category_ID','Category_Arr',
        'Biz_IndexShow','Biz_Index','Biz_MaxPrice','Biz_MinPrice','Region_ID','Biz_Notice','Biz_Profit','City_ID'
        ,'Group_ID','Finance_Type','Finance_Rate','PC_Skin_ID','pc_banner','pc_slide','pc_bg_color','User_Mobile',
        'PaymenteRate','Invitation_Code','Biz_PayConfig','Biz_Flag','is_agree','is_auth','is_pay','bond_free','is_biz',
        'expiredate','addtype','bill_reduce_type','bill_man_json','bill_rebate','Finance_Type2','Biz_KfProductCode',
        'QrcodeSet','qiword','Biz_stmdShow', 'barnerswitch'
    ];

    protected $hidden = ['Biz_PassWord'];

    //所属分组
    public function group()
    {
        return $this->belongsTo(Biz_Group::class, 'Group_ID', 'Group_ID');
    }

}
