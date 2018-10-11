<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dis_Config extends Model
{
    protected  $primaryKey = "id";
    protected  $table = "distribute_config";
    public $timestamps = false;

    protected $fillable = [
        'Users_ID','Dis_Level','Dis_Mobile_Level','Dis_Self_Bonus','Distribute_Type','Distribute_Audit','Withdraw_Type',
        'Withdraw_Date','Withdraw_Limit','Withdraw_PerLimit','Distribute_Customize','Dis_Agent_Type','Agent_Rate',
        'Agent_Rate_Commi','Distribute_Share','Distribute_ShareScore','QrcodeBg','ApplyBanner','HIncomelist_Open',
        'H_Incomelist_Limit','Balance_Ratio','Poundage_Ratio','Pro_Title_Level','Pro_Title_Status','Pro_Title_Rate',
        'Level_UpdateAuto','Index_Professional_Json','Distribute_Limit','Distribute_Agreement','Distribute_AgreementTitle',
        'Distribute_AgreementOpen','Distribute_ShopOpen','TxCustomize','bottom_styles','Withdraw_Switch','Distribute_Form',
        'Dis_Agreement','Distribute_UpgradeWay','Reserve_DisplayName','Reserve_DisplayTelephone','Button_Name',
        'Button_Color','Level_Form','pop_helper_pic','pop_helper_descr','Jobs_Title_Level','Jobs_Title_Status',
        'Title_Dislevel','nicheng_after','TxConsume','many_switch','Reserve_DisplayLSX','Bindmobile','Fbonsnameswitch'
    ];

    //爵位名称数组
    public function get_dis_pro_rate_title()
    {
        $dis_config = static::first(array('Pro_Title_Rate'));

        $pro_titles = false;
        if (!empty($dis_config)) {

            $pro_titles = json_decode($dis_config->Pro_Title_Rate, TRUE);
            if(!empty($pro_titles)){
                foreach($pro_titles as $key=>$item){
                    if($key != 'Level_Num'){
                        if(strlen($item['Name']) == 0){
                            unset($pro_titles[$key]);
                        }
                    }
                    unset($pro_titles['Level_Num']);
                }
                ksort($pro_titles);
            }
        }
        return $pro_titles;

    }
}
