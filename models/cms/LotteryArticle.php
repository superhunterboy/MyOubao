<?php

class LotteryArticle extends BaseModel {

//       protected static $cacheLevel = self::CACHE_LEVEL_FIRST;
//    protected static $cacheMinutes = 60;
    public static $resourceName = 'LotteryArticle';

    /**
     * title field
     * @var string
     */
    public static $titleColumn = 'title';
    public $orderColumns = [
        'is_top' => 'desc',
        'updated_at' => 'desc'
    ];
    public static $mobileColumns = [
        'id',
        'title',
        'created_at'
    ];

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'category_id',
        'title',
        'summary',
        'search_text',
        'add_user_id',
        'status',
        'is_for_agent',
        'update_user_id',
        'created_at'
    ];

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = [
        'title' => 'required|max:50',
        'summary' => 'max:50',
        'content' => 'required',
        'search_text' => 'max:50000',
        'category_id' => 'required|integer',
        'status' => 'required|in:0,1,2,3',
        'is_for_agent' => 'required|in:0,1',
    ];
    protected $table = 'cms_news_articles';
    public static $htmlSelectColumns = [
        'category_id' => 'aCategories',
        'add_user_id' => 'aAdmins',
        'update_user_id' => 'aAdmins',
        'status' => 'aStatus',
    ];
    protected $fillable = [
        'category_id',
        'title',
        'summary',
        'content',
        'search_text',
        'is_for_agent',
        'status',
    ];
    const TYPE_HELP = 1;
    const TYPE_ANNOUMCEMENT = 2;
    const STATUS_NEW = 0; // 待审核
    const STATUS_AUDITED = 1; // 审核通过
    const STATUS_REJECTED = 2; // 审核未通过
    const STATUS_RETRACT = 3; // 公告下架
    const STATUS_TOP_ON = 1;
    const STATUS_TOP_OFF = 0;
    const NUMBERPARENTID = 1;
    const JCPARENTID = 2;
    public static $aStatusDesc = [
        self::STATUS_NEW => 'new',
        self::STATUS_AUDITED => 'audited',
        self::STATUS_REJECTED => 'rejected',
        self::STATUS_RETRACT => 'retract',
    ];
    protected function beforeValidate() {
        if (!Session::get('admin_user_id')) {
            return false;
        }
        $this->add_user_id = Session::get('admin_user_id');
        return parent::beforeValidate();
    }
    /**
     * 获取最新的并且置顶的1条记录
     */
    public static function getNewsJcTopArticle(){
//        $aId = LotteryCategory::getJcLotteryCateId();
//        if (empty( $aId )) {
//            return ;
//        }
        return self::where('is_top', self::STATUS_TOP_ON)->where('status', self::STATUS_AUDITED)->where('category_id', self::JCPARENTID)->orderBy('created_at', 'desc')->take(1)->get();
    }
    
       /**
     * 获取最新的并且置顶的1条记录
     */
    public static function getNewsNumberTopArticle(){
//        $aId = LotteryCategory::getNumberLotteryCateId();
//        if (empty( $aId )) {
//            return ;
//        }
        return self::where('is_top', self::STATUS_TOP_ON)->where('status', self::STATUS_AUDITED)->where('category_id', self::NUMBERPARENTID)->orderBy('created_at', 'desc')->take(1)->get();
    }
    
    /**
     * 获取最新的5条数字彩资讯
     * @return boolean
     */
    public static function getNewNumberArticle(){
//        $aId = LotteryCategory::getNumberLotteryCateId();
//        if (empty( $aId )) {
//            return ;
//        }
         return self::where('is_top', self::STATUS_TOP_OFF)->where('status', self::STATUS_AUDITED)->where('category_id', self::NUMBERPARENTID)->orderBy('created_at', 'desc')->take(6)->get();
    }
     /**
     * 获取最新的5条竞技彩资讯
     * @return boolean
     */
    public static function getNewJcArticle(){
//        $aId = LotteryCategory::getJcLotteryCateId();
//        if (empty( $aId )) {
//            return ;
//        }
         return self::where('is_top', self::STATUS_TOP_OFF)->where('status', self::STATUS_AUDITED)->where('category_id', self::JCPARENTID)->orderBy('created_at', 'desc')->take(6)->get();
    }
    
    public static function getArticlePidById($id){
        return self::where('id', $id)->first();
    }
    
    public static function getCateGoryById($id){
         return LotteryCategory::where('id', $id)->first();
    }
}
