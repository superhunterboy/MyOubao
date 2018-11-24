<?php

class LotteryCategory extends BaseModel {

    public static $resourceName = 'Category';
    protected $table = 'cms_news_categories';
    /**
     * title field
     * @var string
     */
    public static $titleColumn = 'name';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'id',
        'name',
        'parent',
        'template'
    ];
    public static $treeable = true;

    /**
     * the main param for index page
     * @var string 
     */
    public static $mainParamColumn = 'parent_id';

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|max:50',
        'template' => 'max:50',
        'parent_id' => ''
    ];
    
    public static $htmlSelectColumns = [
        'parent_id' => 'aCategoriesTree',
        'template' => 'aTemplates',
    ];
    
    
    protected $fillable = [
        'parent_id',
        'parent',
        'name',
        'template',
    ];


    /**
     * 获取竞彩的cate id
     * @return type
     */
    public static function getJcLotteryCateId(){
        $aIds = self::where('parent_id', 2)->get();
        $aId = [];
        foreach ($aIds as $k=>$v) {
            $aId[] = $v->id;
        }
        return $aId;
    }
  /**
     * 获取数字彩的cate id
     * @return type
     */
    public static function getNumberLotteryCateId(){
        $aIds = self::where('parent_id', 1)->get();
        $aId = [];
        foreach ($aIds as $k=>$v) {
            $aId[] = $v->id;
        }
        return $aId;
    }
  
    public static function getCategoryPid(){
        return self::where('parent_id', null)->get();
    }
    
    public static function getChildrenById($id){
        return self::where('parent_id', $id)->get();
    }
    
    public static function getJcAndNumber(){
        return self::wherein("id", [LotteryArticle::NUMBERPARENTID, LotteryArticle::JCPARENTID])->get();
    }
}
