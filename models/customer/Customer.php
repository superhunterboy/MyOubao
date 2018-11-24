<?php

/*
 * 商户类，管理移动端和浏览器端商户
 */

class Customer extends BaseModel {
    
    const ERRNO_MISSING_DATA = -2601;

    protected static $cacheLevel = self::CACHE_LEVEL_FIRST;

    /**
     * 开启状态
     */
    const STATUS_ON = 1;

    /**
     * 关闭状态
     */
    const STUATUS_OFF = 0;

    public static $resourceName = 'Customer';

    /**
     * title field
     * @var string
     */
    public static $titleColumn = 'name';
    public $orderColumns = [
        'created_at' => 'asc'
    ];

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'name',
        'email',
        'domain',
        'ip',
        'key',
        'status',
        'created_at',
    ];

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|max:50',
        'email' => 'required|max:100|email',
        'domain' => 'required|max:100',
        'ip' => 'max:255',
        'status' => 'in:0,1',
    ];
    protected $table = 'customers';
    public static $htmlSelectColumns = [
    ];
    protected $fillable = [
        'name',
        'email',
        'domain',
        'ip',
        'key',
        'status',
    ];

    /**
     * run before save()
     */
    protected function beforeValidate() {
        $security_key = Config::get('security.salt');
        $this->key = md5(md5(md5($this->name . $this->email . $this->domain . $security_key . time())));
        return parent::beforeValidate();
    }

    public static function getCustomerByKey($customerKey) {
        if (static::$cacheLevel == self::CACHE_LEVEL_NONE) {
            return parent::where('key', '=', $customerKey)->where('status', '=', self::STATUS_ON)->get()->first();
        }
        Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);

        $key = self::createCacheKey($customerKey);
        if ($aAttributes = Cache::get($key)) {
            $obj = new static;
            $obj = $obj->newFromBuilder($aAttributes);
        } else {
            $obj = parent::where('key', '=', $customerKey)->where('status', '=', self::STATUS_ON)->get()->first();
            if (!is_object($obj)) {
                return false;
            }
            Cache::forever($key, $obj->getAttributes());
        }

        return $obj;
    }

    public static function getCustomerAll(){

        if (static::$cacheLevel == self::CACHE_LEVEL_NONE) {
            return parent::where('status', '=', self::STATUS_ON)->get()->toArray(['id','name']);
        }

        Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);

        $key = self::createCacheKey(__FUNCTION__);

        if ($aCustomers = Cache::get($key)) {
            return $aCustomers;
        } else {
            $aCustomers = parent::where('status', '=', self::STATUS_ON)->get()->toArray(['id', 'name']);
            Cache::forever($key, $aCustomers);
        }

        return $aCustomers;
    }

}
