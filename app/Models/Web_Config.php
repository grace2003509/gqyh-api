<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Web_Config extends Model
{
    protected  $primaryKey = "Users_ID";
    protected  $table = "web_config";
    public $timestamps = false;

    protected $fillable = ['Users_ID','SiteName','CallEnable','CallPhoneNumber','MusicPath','Animation','Skin_ID',
        'PagesShow','ShowTime','PagesPic','Trade_ID','Stores_Name','Stores_LBS','Stores_Address',
        'Stores_Description','Stores_ImgPath','Stores_PrimaryLng','Stores_PrimaryLat'];
}
