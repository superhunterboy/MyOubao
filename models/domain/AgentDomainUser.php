<?php

class AgentDomainUser extends BaseModel {

    protected $table = 'agent_domain_users';


    protected $fillable = [
        'id',
        'user_id',
        'group_id',
        'created_at',
        'updated_at',
    ];
    public static $resourceName = 'AgentDomainUser';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'id',
        'user_name',
        'group_name',
        'domain',
    ];
    public static $listColumnMaps = [
        'user_name' => 'friendly_user_name',
        'group_name'=> 'friendly_group_name',
        'domain'    => 'friendly_domain',
    ];

    public static $htmlSelectColumns = [
        'group_id'=> 'aDomainGroup',
    ];
    /**
     * 下拉列表框字段配置
     * @var array
     */

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'id' => 'asc'
    ];

    /**
     * the main param for index page
     * @var string
     */
    public static $mainParamColumn = 'id';
    public static $rules = [
        'user_id'=>'required|integer',
        'group_id'=>'required|integer',
    ];
    public function getFriendlyUserNameAttribute(){
        $oUser = UserUser::find($this->user_id);
        if($oUser){
            return $oUser->username;
        }else{
            return '';
        }
    }
    public function getFriendlyDomainAttribute(){
        $oDomains = AgentDomain::where('group_id',$this->group_id)->get(array('domain'));

        $domains = '';
        foreach($oDomains as $do){
            $domains .=$do->domain.'     <br/>';
        }
        return $domains;
    }
    public function getFriendlyGroupNameAttribute(){
        $oGroup = AgentDomainGroup::find($this->group_id);
        if($oGroup){
            return $oGroup->group_name;
        }else{
            return '';
        }
    }

    static public function getDomainGroupIdByUserId($userId){
        $data = self::where('user_id',$userId)->get(array('group_id'));
        $group_id='';
        if($data){
            $data = $data->toArray();
            // $queries = DB::getQueryLog();
            // $last_query = end($queries);
            // pr($last_query);exit;
            $iCount = count($data);
            $sDomain = '';
            if ($iCount) {
                $iIndex = $iCount > 1 ? rand(0, $iCount - 1) : 0;
                $group_id = $data[$iIndex]['group_id'];
            }

        }
        return $group_id;

    }




}
