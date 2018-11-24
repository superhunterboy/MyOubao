<?php

class CmsCategory extends BaseModel {

    public static $resourceName = 'Category';

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
        'template' => 'max:50'
    ];
    protected $table = 'cms_categories';
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

    const HELP_CENTER = 'help';
    const HELP_ID = '3';

    public static function getHelpCenterCategories() {
        return CmsCategory::where('template', '=', static::HELP_CENTER)->get();
    }

    public static function getCmsCategoryTypes($parent_id) {
        $aTypes = self::where('parent_id', $parent_id)->orWhere('id', $parent_id)->get(['id','name'])->toArray();
        return $aTypes;
    }

}
