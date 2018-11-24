<?php

/*
 * 字典模型类
 * 作用：管理语言包词汇
 */

class Dictionary extends BaseModel {

    
    public static $resourceName = 'Dictionary';
    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'name',
        'models',
        'en_column',
        'zh_column',
    ];

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = [
        'name'      => 'required|max:64',
        'models'    => 'max:512',
        'en_column'    => 'max:64',
        'zh_column'    => 'max:64',
    ];
    public static $mainParamColumn = 'name';
    protected $fillable = [
        'name',
        'models',
        'en_column',
        'zh_column',
    ];

   
    protected $table = 'dictionaries';
    public static $titleColumn = 'name';

}

