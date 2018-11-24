<?php

class AdInfo extends BaseModel {
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'ad_infos';
    public static $resourceName = 'AdInfo';
    public static $titleColumn = 'name';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
    protected $guarded = [];
    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = false;
    public static $mainParamColumn = 'ad_location_id';
    protected $fillable = [
        'name',
        'ad_location_id',
        'pic_url',
        'content',
        'redirect_url',
        'is_closed',
        'creator_id',
        'creator_name',
    ];
    public static $columnForList = [

        'name',
        'content',
        'pic_url',
        'is_closed',
        'redirect_url',
        // 'creator_id',
        'creator_name',
        'ad_location_id',

    ];
    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        'ad_location_id' => 'aLocations',
    ];
    public $orderColumns = [
        'id' => 'asc'
    ];
    public static $rules = [
        'name'           => 'required|max:50',
        'ad_location_id' => 'required|integer',
        'pic_url'        => '',
        'content'        => 'required',
        'redirect_url'   => 'required',
        'is_closed'      => 'required|in:0,1',

    ];

   const NUM_AD_TYPE = 3;

    protected function beforeValidate(){
        $this->creator_id = Session::get('admin_user_id');
        $this->creator_name = Session::get('admin_username');

        return parent::beforeValidate();
    }

public static function getLatestRecords()
    {
        $aColumns = ['id','pic_url','updated_at','redirect_url','ad_location_id','content'];
        // TODO 公告是否需要绑定用户待定
        // $iUserId = Session::get('user_id');
        // $oUser = User::find($iUserId);
        // if (Session::get('is_agent')) {
        //     $aUserIds = [];
        //     $aUsers = $oUser->getUsersBelongsToAgent();
        //     foreach ($aUsers as $oUser) {
        //         $aUserIds[] = $oUser->id;
        //     }
        //     $oQuery = self::whereIn('user_id', $aUserIds);
        // } else {
        //     $oQuery = self::where('user_id', '=', $iUserId);
        // }
        $aArticles = self::where('ad_location_id' , '=' , self::NUM_AD_TYPE)->orderBy('updated_at', 'desc')->get($aColumns);
        return $aArticles;
    }

    public static function getAdInfosByLocationId($iLocationId = null)
    {
        $aColumns = ['id', 'name' ,'content', 'pic_url', 'redirect_url', 'ad_location_id'  ];
        return self::where('ad_location_id', '=', $iLocationId)->where('is_closed', '=', 0)->get($aColumns);
    }

}