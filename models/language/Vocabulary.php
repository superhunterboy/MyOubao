<?php

/*
 * 词汇模型类
 * 作用：生成语言包词汇以及导出语言包文件
 */

class Vocabulary extends BaseModel {

    public static $resourceName = 'Vocabulary';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'dictionary',
        'keyword',
        'en',
        'zh_cn',
    ];

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = [
        'dictionary_id' => 'integer',
        'keyword' => 'max:100',
        'en' => 'max:512',
        'zh_cn' => 'max:512',
    ];
    protected $table = 'vocabularies';
    public static $htmlSelectColumns = [
        'dictionary_id' => 'aDicts',
    ];
    protected $fillable = [
        'dictionary_id',
        'directory',
        'keyword',
        'en',
        'zh_cn',
    ];

    public $orderColumns = ['keyword' => 'asc'];

    protected function beforeValidate(){
        if (!$this->dictionary_id) return false;
        $oDictionary = Dictionary::find($this->dictionary_id);
        if (empty($oDictionary)) return false;
        $this->dictionary = $oDictionary->name;
        return parent::beforeValidate();
    }
}
