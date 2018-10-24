<?php
/**
 * 分销账户Model
 */

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dis_Account extends Model {

	use SoftDeletes;
	protected $dates = ['deleted_at'];
	
	protected $fillable = ['Users_ID', 'User_Name', 'Account_ID', 'invite_id', 'balance',
		'Total_Income', 'Enable_Agent', 'User_ID', 'Account_CreateTime', 'Shop_Name',
		'Shop_Logo', 'Dis_Path', 'status', 'Ex_Bonus', 'Is_Audit','Is_Dongjie','Is_Delete',
		'Up_Group_Num', 'Group_Num', 'Professional_Title', 'last_award_income','sha_level','Level_ID'];


	protected $primaryKey = "Account_ID";
	protected $table = "distribute_account";
	public $timestamps = false;
	public $descendants = null;
	

	//一个分销账号属于一个用户
	public function user() {
		return $this->belongsTo(Member::class, 'User_ID', 'User_ID');
	}

	//获取此分销商的邀请人
	public function inviter() {
		return $this->belongsTo(Member::class, 'invite_id', 'User_ID');
	}

	//一个分销账户拥有多个代理地区
	public function disAreaAgent() {
		return $this->hasMany(Dis_Agent_Area::class, 'Account_ID', 'Account_ID');
	}

	/*一个分销账号拥有多个分销记录*/
	public function disRecord() {
		return $this->hasMany(Dis_Record::class, 'Owner_ID', 'User_ID');
	}

	/*一个分销账号拥有多个发钱记录*/
	public function disAccountRecord() {
		return $this->hasManyThrough(Dis_Account_Record::class, 'Dis_Record', 'Owner_ID', 'Record_ID');
	}

	/*一个分销账号拥有多个得钱记录*/
	public function disAccountPayRecord() {
		return $this->hasMany(Dis_Account_Record::class, 'User_ID', 'User_ID');
	}


    /**
     * 获取此分销账号的祖先id列表
     * @param 本店当前分销级数 int $level
     * @return Array $ids 祖先id列表
     */
    function getAncestorIds($level = 0, $self = 0)
    {
        $ids = array();
        if (!empty($this->Dis_Path)) {

            $list = explode(',', $this->Dis_Path);
            $list = array_unique($list);//数组去重
            $list = array_filter($list);//去除空值

            if ($level && $level <= count($list)) {
                $cut_num = count($list) - $level;
                $ids = array_slice($list, $cut_num-1);//截取数组
            } else {
                $ids = $list;
            }

            foreach ($ids as $key => $item) {
                $ids[$key] = intval($item);
            }

        }

        if($self > 0) $ids = array_push($ids, $this->User_ID);

        return array_reverse($ids);
    }


	/**
	 * 统计某一时间段内加入的分销商数量
	 * @param  string $Users_ID   本店唯一标识
	 * @param  string $Begin_Time 开始时间
	 * @param  string  $End_Time  结束时间
	 * @return int     $num     分销商数量
	 */
	public function accountCount($Begin_Time, $End_Time) {
		$num = $this->whereBetween('Account_CreateTime', [$Begin_Time, $End_Time])
			->count();

		return $num;
	}

	/**
	 * 生成本分销账号的Dis_Path
	 * @return string $Dis_Path
	 */
	public function generateDisPath() {

		$Dis_Path = '';

		$user = $this->user()->getResults();
		$userOwnerID = $user->Owner_Id;
		$invite_id = $this->invite_id;
		$inviter = $this->inviter()->getResults();

		if ($invite_id != 0) {
			if(!$inviter){
				return $Dis_Path;
			}
			$inviterDisPath = $inviter->disAccount()
				->getResults()
				->Dis_Path;
			$ids = explode(',', trim($inviterDisPath, ',,'));
			$num = count($ids);
			$pre = strlen($inviterDisPath) ? '' : ',';
			$Dis_Path = $pre . $inviterDisPath . $userOwnerID . ',';

		} else {
			//如果不是根店
			if ($userOwnerID != 0) {
				$Dis_Path = ',' . $userOwnerID . ',';
			}
		}

		return $Dis_Path;
	}

	/**
	 * 统计分销商各个级别有下属的数目
	 * @param  int    $level 本商城分销层数
	 * @return array  $levelNum 每个级别的下属数目
	 *                example  [1=>3,2=>4,5=6]
	 */
	public function getLevelNum($level) {

		$levelList = $this->getLevelList($level);
	
		$levelNum = array();
		if(!empty($levelList)){
			foreach($levelList as $level=>$collection){
				$levelNum[$level] = $collection->count();
			}
		}
		
		return $levelNum;
	}

	/**
	 * 获取以级别为索引的下属列表
	 * @param  int   $level 本商城分销层数
	 * @return array $levelList 下属列表
	 */
	public function getLevelList($level){
		
		$posterity = $this->getPosterity($level);
		
		$levelList = array();
		for($i=1;$i<=$level;$i++){
			$levelList[$i] = $posterity->where('level',$i);
		}

		return $levelList;

	}


	/**
	 * 获取此账号下属分销商
	 * @param  int $level 此店的分销商层数
	 * @return Collection $posterity
	 */
	public function getPosterity($level = 0,$force=false, $type = 'uids') {

		$User_ID = $this->User_ID;
		//获取分销父路径中包含此用户ID的分销商
		$fields = ['Account_ID', 'User_ID', 'Dis_Path','invite_id','Shop_Name','balance',
				'Is_Audit','Total_Income','Account_CreateTime','Shop_Logo','Level_ID','Professional_Title'];

		$ids = [];
		$this->get_Invite_Ids($this, [$User_ID], $ids,$level, 1);
		if($force){
			$ids[] = $User_ID;
		}
		if ($type != 'uids') {
			return $ids;
		} else {
			$flag = $this->whereIn('User_ID', $ids)->get($fields)->filter(function (&$dsAccount) use ($level, $User_ID) {

				//计算出分销商级数
				$dis_path = trim($dsAccount->Dis_Path, ',');
				$dis_path_nodes = explode(',', $dis_path);
				$dis_path_nodes = array_reverse($dis_path_nodes);
				$pos = array_search($User_ID, $dis_path_nodes);
				$curLevel = $pos + 1;

				//为分销账号动态指定级别
				$dsAccount->level = $curLevel;

				if ($curLevel <= $level || $level == 0) {
					return true;
				}
			});

			return $flag;
		}
	}
	
	public function getFirstchild($force=false) {
		$lists = array();
		$User_ID = $this->User_ID;
		//获取分销父路径中包含此用户ID的分销商
		if(empty($this->descendants)||$force){
		 $fields = ['Account_ID', 'User_ID', 'Dis_Path','Shop_Name','balance','invite_id',
		           'Is_Audit','Total_Income','Account_CreateTime'];
		 $this->descendants = $this->where('Users_ID', $this->Users_ID)
			->where('Dis_Path', 'like', '%,' . $User_ID . ',%')
			->get($fields);
		}
		
		//筛选出处于$level级别中的分销商
		$posterity = $this->descendants->toArray();
		
		foreach($posterity as $p){
			$p["Dis_Path"] = substr($p["Dis_Path"],1,-1);
			$arr_temp = explode(",",$p["Dis_Path"]);
			if($arr_temp[count($arr_temp)-1]==$User_ID){
				$lists[$p["User_ID"]] = $p;
				$lists[$p["User_ID"]]["child"] = 0;
			}else{
				$current = array_search($User_ID,$arr_temp);
				$lists[$arr_temp[$current+1]]["child"] = empty($lists[$arr_temp[$current+1]]["child"]) ? 1 : $lists[$arr_temp[$current+1]]["child"]+1;
			}
		}
		return $lists;
	}
	
	/**
	 * 返回此分销账号完整父路径
	 * @param  int $level 此店的分销商层数
	 * @return String $fullDisPath
	 */
	public function getFullDisPath(){
		if(!empty($this->Dis_Path)){
			$fullDisPath = trim($this->Dis_Path,',,');
			$disCollection  = $this->get(array('User_ID','Dis_Path'));
			$disDictionary = get_dropdown_collection($disCollection,'User_ID');
			
			$Dis_Path = trim($this->Dis_Path,',,');
		
			//向上循环，直至找到自己的根店分销商
			while(!empty($Dis_Path)){
				$first = strstr($Dis_Path,',',TRUE);
				$first = $first?$first:$Dis_Path;
				
				$parentDistirbuteAccount = $disDictionary[$first];
				$Dis_Path = $parentDistirbuteAccount->Dis_Path;
				$Dis_Path = trim($Dis_Path,',,');
			    if(!empty($Dis_Path)){
					$fullDisPath =  $Dis_Path.','.$fullDisPath;
				}
			}					
			
		    return  $fullDisPath ;
	
		}else{
			$fullDisPath = '';
		}

		return $fullDisPath ;
		
	}


    function get_distribute_balance_userids($BuyerID,$userids,$distribute_bonus,$type=0)
    {
        $dl_obj = new Dis_Level();
        $level_data = $dl_obj->get_dis_level();
        //此变量用来计算下面拿佣金的会员是第几个分销商级别
        $level_ids = array_flip(array_keys($level_data));
        if(empty($userids)){
            return array();
        }

        //获得每个分销商级别
        $account_list = $this->whereIn('User_ID',$userids)
            ->get(array('Level_ID','User_ID'))
            ->map(function($account){
                return $account->toArray();
            })->all();
        $accounts = array();
        foreach($account_list as $account){
            $accounts[$account['User_ID']] = array(
                'limit'=>empty($level_data[$account['Level_ID']]) ? array() : json_decode($level_data[$account['Level_ID']]['Level_PeopleLimit'], true),
                'bonus'=>empty($distribute_bonus[$account['Level_ID']]) ? array() : $distribute_bonus[$account['Level_ID']],
                'level' => $level_ids[$account['Level_ID']]
            );
        }

        //循环数组筛选
        $result = array();
        foreach($userids as $key=>$value){
            $result[$value]['level'] = $accounts[$value]['level'];
            //该用户在这条线上的级别为$key+1
            $lid = $key+1;
            if ($lid <= 3) {

                if(empty($accounts[$value]['limit'])){//不存在该分销商级别人数限制，忽略
                    $result[$value] = array('status'=>0,'msg'=>'你的分销商级别不存在');
                    continue;
                }

                if(empty($accounts[$value]['bonus'])){//不存在该分销商级别佣金设置，忽略
                    $result[$value] = array('status'=>0,'msg'=>'该分销商级别佣金未设置');
                    continue;
                }

                if(!isset($accounts[$value]['limit'][$lid])){//限制人数没有到此级别，忽略
                    $result[$value] = array('status'=>0,'msg'=>'分销商级别设置有误，该分销级别的限制人数未设置');
                    continue;
                }

                if(!isset($accounts[$value]['bonus'][$key])){//限制人数没有到此级别，忽略
                    $result[$value] = array('status'=>0,'msg'=>'分销佣金设置有误，该分销级别的分销佣金未设置');
                    continue;
                }

                if($accounts[$value]['limit'][$lid]==-1){//禁止获得该级别佣金，忽略
                    $result[$value] = array('status'=>0,'msg'=>'你的分销商级别禁止获得'.$lid.'级佣金');
                    continue;
                }

                if(empty($record_list[$value])){//该用户未获得过佣金
                    $result[$value] = array('status'=>1,'bonus'=>$accounts[$value]['bonus'][$key]);
                    $result[$value]['level'] = $accounts[$value]['level'];
                    continue;
                }

                if(empty($record_list[$value][$lid])){//该用户未获得过该级别佣金
                    $result[$value] = array('status'=>1,'bonus'=>$accounts[$value]['bonus'][$key]);
                    $result[$value]['level'] = $accounts[$value]['level'];
                    continue;
                }

                if($accounts[$value]['limit'][$lid]==0){//该级别佣金不限制
                    $result[$value] = array('status'=>1,'bonus'=>$accounts[$value]['bonus'][$key]);
                    $result[$value]['level'] = $accounts[$value]['level'];
                    continue;
                }

                if(in_array($BuyerID,$record_list[$value][$lid])){//已经获得此人的佣金
                    $result[$value] = array('status'=>1,'bonus'=>$accounts[$value]['bonus'][$key]);
                    $result[$value]['level'] = $accounts[$value]['level'];
                    continue;
                }

                if($accounts[$value]['limit'][$lid]>0 && count($record_list[$value][$lid])>=$accounts[$value]['limit'][$lid]){//获得过的该级别佣金未达到最大值
                    $result[$value] = array('status'=>0,'msg'=>'你获得的'.$lid.'级佣金人数级已达到最大值');
                    continue;
                }

                if($accounts[$value]['limit'][$lid]>0 && count($record_list[$value][$lid])<$accounts[$value]['limit'][$lid]){//获得过的该级别佣金未达到最大值
                    $result[$value] = array('status'=>1,'bonus'=>$accounts[$value]['bonus'][$key]);
                    $result[$value]['level'] = $accounts[$value]['level'];
                    continue;
                }
            } else {
                //多级统一走key为999的设置
                if (!empty($accounts[$value]['bonus'][999])) {
                    $result[$value] = array('status'=>1,'bonus'=>$accounts[$value]['bonus'][999]);
                } else {
                    $result[$value] = array('status'=>1,'bonus'=>0);
                }
                $result[$value]['level'] = $accounts[$value]['level'];
            }
        }
        return $result;
    }


    public function get_Invite_Ids($obj, array $param, &$userids, $level, $deepth = 1){
        $result = $obj->whereIn('invite_id', $param)->get(['User_ID'])->toArray();
        if(!empty($result)){
            $arrUserIds = array_map(function($v){
                return $v['User_ID'];
            }, $result);
            $userids = array_merge($userids, $arrUserIds);
            if($deepth<$level || $level == 0){
                $this->get_Invite_Ids($obj, $arrUserIds, $userids,$level, ++$deepth);
            }
        }
    }



    /**
     * 获取此账号的祖先id列表(爵位奖专用)
     * @param $user_id $users_id
     * @return array
     */
    public function getUserAncestorIds($owner_id, $ids = '', $level = 0)
    {
        $user = Member::select('Owner_Id', 'User_ID', 'User_Level')->find($owner_id);
        //只识别vip会员和总代
        $ids = '';
        if(@$user->disAccount->status == 1 && @$user->disAccount->Level_ID >= 2) {
            $ids .= $user['User_ID'].',';
        }
        if($user['Owner_Id'] > 0){

            $ids .= $this->getUserAncestorIds($user['Owner_Id'], $ids, 0);
        }

        return $ids;

    }

	

}

?>