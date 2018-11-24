<?php

class ReserveAgent extends BaseModel {

    //表名
    protected $table = 'reserve_agents';
    //软删除
    protected $softDelete = false;
    public static $columnForList = [
        'id',
        'qq',
        'created_at',
        'platform',
        'sale',
    ];
    protected $fillable = [
        'qq',
        'platform',
        'sale',
        'sale_screenshot_path',
    ];
    public $orderColumns = [
        'created_at' => 'desc'
    ];
    public static $htmlSelectColumns = [
        'sale' => 'aSale',
    ];

    //销售额10w
    const SALE_10 = '小于10W';
    const SALE_30 = '10~30';
    const SALE_50 = '30~50';
    const SALE_70 = '50~70';
    const SALE_100 = '70~100';
    const SALE_MORE_THAN_100 = '大于100';

    public static $aSale = [
        0 => self::SALE_10,
        1 => self::SALE_30,
        2 => self::SALE_50,
        3 => self::SALE_70,
        4 => self::SALE_100,
        5 => self::SALE_MORE_THAN_100,
    ];

    /**
     * the main param for index page
     * @var string
     */
    public static $mainParamColumn = 'id';
    public static $rules = [
        'qq' => 'required|integer|unique:reserve_agents,qq',
        'sale' => 'in:0,1,2,3,4,5',
        'platform' => 'max:60',
    ];
    public static $customMessages = [
        'qq.unique' => 'qq必须唯一',
        'qq.required' => '缺少您的qq',
        'qq.integer' => '您的qq填写不正确，请重新填写！',
        'sale.in' => '您的日均销售额选择不正确',
        'platform.max' => '您所代理的平台长度有误，不能超过 :max 个字符',
    ];

    /**
     * 资源名称
     * @var string
     */
    public static $resourceName = 'ReserveAgent';

    /**
     * 取得校验错误信息并转换为字符串返回
     * @return string
     */
    public function & getValidationErrorString() {
        $aErrMsg = [];
        if ($this->isAdmin) {
            // $sLangKey = '_' . Str::slug(static::$resourceName, '_') . '.';
            // pr($sLangKey);exit;
            foreach ($this->validationErrors->toArray() as $sColumn => $sMsg) {
                $aErrMsg[] = implode(',', $sMsg);
            }
        } else {
            foreach ($this->validationErrors->toArray() as $sMsg) {
                $aErrMsg[] = implode(',', $sMsg);
            }
        }
        $sError = implode(' ', $aErrMsg);
        // pr($sError);exit;
        return $sError;
    }

}
