<?php

/*
 * 词汇模型类
 * 作用：生成语言包词汇以及导出语言包文件
 */

class CmsArticle extends BaseModel {

    protected static $cacheLevel = self::CACHE_LEVEL_FIRST;
    protected static $cacheMinutes = 60;
    public static $resourceName = 'Article';

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
    protected $table = 'cms_articles';
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

    // protected function afterSave($bSucc) {
    //     // pr($this->aFiles);
    //     // exit;
    //     return parent::afterSave($bSucc);
    // }

    public static function getLatestRecords($iCount = 6) {
        $aColumns = ['id', 'title', 'content', 'updated_at', 'category_id', 'created_at'];
        $categorys = CmsCategory::getCmsCategoryTypes(CmsArticle::TYPE_ANNOUMCEMENT);
        $oQuery = self::whereIn('category_id', array_column($categorys, 'id'))
                ->where('status', 1);
        if (!$is_agent = Session::get('is_agent')) {
            $oQuery->where('is_for_agent', 0);
        }
        $aArticles = $oQuery->orderBy('updated_at', 'desc')->take($iCount)->get($aColumns);

        $aCategoryTypes = array_combine(array_column($categorys, 'id'), array_column($categorys, 'name'));
        foreach ($aArticles as $aArticle) {
            if ($aArticle->category_id != CmsArticle::TYPE_ANNOUMCEMENT) {
                $aArticle->title = '[' . $aCategoryTypes[$aArticle->category_id] . '] ' . $aArticle->title;
            }
        }
        return $aArticles;
    }

    protected function getTitleFormattedAttribute() {
        $oCategory = CmsCategory::find($this->category_id);

        if ($oCategory->parent_id) {
            return '[' . $oCategory->name . '] ' . $this->title;
        }
        return $this->title;
    }

    public static function getHelpCenterArticles() {
        $aCategories = CmsCategory::getHelpCenterCategories();
        $aCategoryIds = [];
        foreach ($aCategories as $key => $value) {
            $aCategoryIds[] = $value->id;
        }

        return CmsArticle::whereIn('category_id', $aCategoryIds)->get();
    }

    public static function getArticlesByCaregoryId($iCategoryId) {
        return CmsArticle::where('category_id', '=', $iCategoryId)->get();
    }

    public static function getNewArticle() {
        return self::where('status', self::STATUS_AUDITED)->orderBy('created_at', 'desc')->take(3)->get();
    }

    public function getCreatedAtDayAttribute() {
        return date('m/d', strtotime($this->created_at));
    }

}
