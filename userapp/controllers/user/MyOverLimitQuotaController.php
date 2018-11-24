<?php
class MyOverLimitQuotaController extends UserBaseController {
    
    /**
     * 资源模型名称
     * @var string|Illuminate\Database\Eloquent\Model
     */
    protected $resourceView = 'centerUser.myOverlimitQuota';
    protected $modelName = 'OverlimitPrizeGroup';
 
    
    public function index($user_id=null){
        if(!Session::get('is_agent'))  App::abort(404);
        $parent_user_id = Session::get("user_id");

        $bIsOverLimitPrizeGroup = Session::get('show_overlimit');
        if(!$bIsOverLimitPrizeGroup)
            return $this->goBack('error', __('_overlimitprizegroup.no_quota'));//暂无配额管理
        $quotas = OverlimitPrizeGroup::getPrizeGroupByTopAgentId($parent_user_id);
        $this->setVars(compact('quotas','bIsOverLimitPrizeGroup'));
        $aSubUsers = [];
        $sCurrentTab = 'all';
        $username = isset($this->params['username']) ? e(trim($this->params['username'])) : null;
              

        $prize_group = isset($this->params['prize_group']) ? e(trim(Input::get('prize_group'))) : null;
        $aColumns = ['id','username','prize_group'];
        if($user_id){
            $aOSubUsers=  User::where('id',$user_id)->where('blocked',0)->where('is_agent',1)->where('parent_id',$parent_user_id)->get($aColumns);
        }
        elseif($username){
            $aOSubUsers = User::where('parent_id',$parent_user_id)->where('is_agent',1)->where('blocked',0)->where('username',$username)->get($aColumns);
        } 
        elseif($prize_group){

            $aOSubUsers = DB::select("select u.id,u.username,u.prize_group  "
                    . "from overlimit_prize_group o "
                    . "right join users u  on   u.id = o.top_agent_id "
                    . "where (u.prize_group = ".$prize_group."  or  o.classic_prize_group=".$prize_group." ) "
                    . "and u.parent_id=".$parent_user_id." and u.is_agent=1 and u.blocked=0");
            $sCurrentTab = $prize_group;
            
        }
        else{
            
            $aOSubUsers=  User::where('blocked',0)->where('prize_group',">",  SysConfig::readValue(PrizeSysConfig::AGENT_MAX_PRIZE_GROUP))->where('is_agent',1)->where('parent_id',$parent_user_id)->get($aColumns);
        }

        $aSubUserIds = [];
        foreach($aOSubUsers as $user){
            array_push($aSubUserIds, $user->id);
            $iRealPrizeGroup = UserPrizeGroupTmp::getForeverPrize($user);
            $aSubUsers[$user->id]['info'] =  ['username'=>$user->username,
                'prize_group'=>$user->prize_group,
                'forever_prize_group'=>$iRealPrizeGroup,
            ];
            $aSubUsers[$user->id]['object'] = $user;
            
        }
       
        foreach($aSubUsers as $key=>$user){
            foreach(OverlimitPrizeGroup::getHighPrizeGroups() as $p_group){
                $aSubUsers[$key]['quotas'][$p_group]['value'] = 0;
                $aSubUsers[$key]['quotas'][$p_group]['editable'] = false;
                if($user['info']['forever_prize_group'] >= $p_group)
                    $aSubUsers[$key]['quotas'][$p_group]['editable'] = true;
//                if(isset($quotas[$p_group]) && 
//                        $quotas[$p_group]['limit_num'] > $quotas[$p_group]['used_num']
//                        )
//                    $aSubUsers[$key]['quotas'][$p_group]['editable']=true;
 
            }
        }
        if($aSubUserIds){
            
            $aQuotas =  OverlimitPrizeGroup::getPrizeGroupByAgentIds($aSubUserIds);
        
            foreach($aQuotas as $Oquota){
                $aSubUsers[$Oquota->top_agent_id]['quotas'][$Oquota->classic_prize_group]['value'] =  $Oquota->limit_num;
                $aSubUsers[$Oquota->top_agent_id]['quotas'][$Oquota->classic_prize_group]['editable'] = true;
//                echo  UserPrizeGroupTmp::getForeverPrize($aSubUsers[$Oquota->top_agent_id]['object']);
//                echo $Oquota->classic_prize_group.'<br />-----------';
                if( (int)  UserPrizeGroupTmp::getForeverPrize($aSubUsers[$Oquota->top_agent_id]['object']) < (int) $Oquota->classic_prize_group) $aSubUsers[$Oquota->top_agent_id]['quotas'][$Oquota->classic_prize_group]['editable'] = false;
            }
        }
//        pr($aSubUsers);
//        exit;
        $this->setVars('aSubUsers',$aSubUsers);
        $this->setVars('sCurrentTab',$sCurrentTab);
        return parent::index();
         
    }


    /**
     * 获取将进组信息和历史变更信息
     */
    public function getQuotaAndHistory()
    {
        $input = Input::all();
        if (!isset($input['user_id']) || !isset($input['prize_group'])) {
            return Response::json(['status' => 0, 'error_info' => 'params error']);
        }
        $user_id = e(trim($input['user_id']));
        $prize_group = e(trim($input['prize_group']));
        $parent_id = Session::get('user_id');
        $oUser = User::find($user_id);
        //奖金组合法性
        $aHighPrizeGroups=OverlimitPrizeGroup::getHighPrizeGroups();
        if (!isset($aHighPrizeGroups[$prize_group])) {
            $aReturnMsg['error'] = __('_user.prizegroup-not-exit');
            return Response::json($aReturnMsg);

        }
        //用户是否存在
        if (!$oUser) {
            $aReturnMsg['error'] = __('_user.missing-user');
            return Response::json($aReturnMsg);
        } else {
            $aUser = $oUser->getAttributes();
        }
        //是否下级
        $forefather_ids = explode(',', $aUser['forefather_ids']);
        if (!in_array($parent_id, $forefather_ids)) {
            $aReturnMsg['error'] = __('_user.missing-user');
            return Response::json($aReturnMsg);
        }
        $aDatas = [
            'isSuccess' => 1,
            'type' => 'success',
            'data' => []
        ];
        $OverlimitPrizeGroup = new OverlimitPrizeGroup();
        //获取高点配额
        $oQuotas = OverlimitPrizeGroup::getDatasByPrizeGroupAndTopAgentId($user_id, $prize_group);
        if (empty($oQuotas)) {
            $aDatas['data']['userQuota'] = ['user_id' => $aUser['id'], 'username' => $aUser['username'], 'prize_group' => $prize_group, 'limit_num' => 0, 'used_num' => 0];
        } else {
            $aQuotas = $oQuotas->getAttributes();
            $aDatas['data']['userQuota'] = [
                'user_id' => $aUser['id'],
                'username' => $aUser['username'],
                'id' => $aQuotas['id'],
                'prize_group' => $aQuotas['classic_prize_group'],
                'limit_num' => $aQuotas['limit_num'],
                'used_num' => $aQuotas['used_num']
            ];
        }
        //父级高点配额
        $oParentQuotas = OverlimitPrizeGroup::getDatasByPrizeGroupAndTopAgentId(session::get('user_id'), $prize_group);

        if (empty($oParentQuotas)) {
            $aParentQuota = ['prize_group' => $prize_group, 'limit_num' => 0, 'used_num' => 0];
        } else {
            $aParentQuota = $oParentQuotas->getAttributes();
        }
        $aDatas['data']['userQuota']['parent_limit_num'] = $aParentQuota['limit_num'];
        $aDatas['data']['userQuota']['parent_used_num'] = $aParentQuota['used_num'];
        //获取历史信息
        $aDatas['data']['history'] = UserPrizeHistory::where('assign_agent_id', $user_id)
            ->where('agent_id', $parent_id)
            ->where('prize_group', $prize_group)
            ->get(['prize_group', 'plus_num', 'subtract_num', 'note', 'created_at'])
            ->toArray();
        foreach ($aDatas['data']['history'] as &$value) {
            $value['created_at'] = date('Y-m-d', strtotime($value['created_at']));
        }
        return Response::json($aDatas);
    }
    
    public function save(){
        if(!Request::isMethod('POST'))
        {
            App::abort(403);
        }
        $data=Input::all();
        if(!isset($data['user_id']) || !isset($data['prize_group']))return $this->goBack('error', __('_overlimitprizegroup.data-error'));
        if($data['plus_num'] < 0 || $data['subtract_num'] < 0) return $this->goBack('error', __('_overlimitprizegroup.data-error'));
        if($data['plus_num'] == 0 && $data['subtract_num'] == 0) 
             return $this->goBack('error', __('_overlimitprizegroup.no_update'));
        $aIncreasingPrizeGroup = [];
        $aDecliningPrizeGroup = [];
        $aParentIncreasingPrizeGroup = [];
        $aParentDecliningPrizeGroup = [];
        if($data['plus_num']){
           if(! OverlimitPrizeGroup::checkPlusNum(Session::get('user_id'), $data['prize_group'],$data['plus_num']))  return $this->goBack('error', __('_overlimitprizegroup.data-error'));
            $oParentUser = User::find($data['user_id']);
            $iParentPrizeGroup = UserPrizeGroupTmp::getForeverPrize($oParentUser);
            
            if($data['prize_group'] > $iParentPrizeGroup) return $this->goBack('error', __('_overlimitprizegroup.beyond_supervisor_prizegroup'));
            $aIncreasingPrizeGroup = [$data['user_id']=>['username'=>$data['username'],
                                                        'change'=>  
                                                                [$data['prize_group']=>$data['plus_num']]],
                                     
                                     ];
            $aParentIncreasingPrizeGroup = [ Session::get('user_id')=>['username'=>Session::get('username'),
                                                        'change'=>
                                                                [$data['prize_group']=>$data['plus_num']]],
                                                               ];
        }
       if($data['subtract_num']){
            if(! OverlimitPrizeGroup::checkSubtractNum($data['user_id'], $data['prize_group'],$data['subtract_num']))  return $this->goBack('error', __('_overlimitprizegroup.data-error'));
         
            $aDecliningPrizeGroup = [$data['user_id']=>['username'=>$data['username'],
                                                        'change'=>  
                                                                [$data['prize_group']=>$data['subtract_num']]]];
        
            $aParentDecliningPrizeGroup = [ Session::get('user_id')=>['username'=>Session::get('username'),
                                                        'change'=>
                                                                [$data['prize_group']=>$data['subtract_num']]],
                                                               ];
            
       }
       
        DB::connection()->beginTransaction();
        try{
            $t= OverlimitPrizeGroup::setPrizeGroupLimitNum($aIncreasingPrizeGroup,$aDecliningPrizeGroup);
           
            if($t){
                $f = OverlimitPrizeGroup::setPrizeGroupUsedNum($aParentIncreasingPrizeGroup,$aParentDecliningPrizeGroup);
                 
                if($f)
                    DB::connection()->commit();
                else{
                    
                    throw new \Exception('update-fails');
                    
                }
                $log = new AgentPrizeGroupLog;
                $log->agent_id = Session::get('user_id');
                $log->assign_agent_id = $data['user_id'];
                $log->prize_group = $data['prize_group'];
                $log->plus_num = isset($data['plus_num'])?$data['plus_num']:'';
                $log->subtract_num =  isset($data['subtract_num'])?$data['subtract_num']:0;
                $log->note = isset($data['note'])?$data['note']:'';
                $logre=$log->save();
                return $this->goBack('success', __('_overlimitprizegroup.update_success'));

             }
             else
             {
              DB::connection()->rollback();
             return $this->goBack('error', __('_overlimitprizegroup.update_fail'));
    }
        }  catch (\Exception $e){
            DB::connection()->rollback();
            return $this->goBack('error', __('_overlimitprizegroup.update_fail'));
        }
    } 

}
 

