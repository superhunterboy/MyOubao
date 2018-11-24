<?php

class CodeCenter extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'code_centers';

    const VALID_TIME_MAX_OFFSET = 86400;
    const VALID_TIME_MIN_OFFSET = -10;
    const ERRNO_REQUEST_NEED_PUSH_AGAIN = 0;
    const ERRNO_REQUEST_ON_SALE = 1;
    const ERRNO_REQUEST_NON_CODE = 2;
    const ERRNO_REQUEST_FINISHED = 4;
    const ERRNO_REQUEST_NON_POST = 8;
    const ERRNO_REQUEST_INVALID = 16;
    const ERRNO_REQUEST_CODECENTER_ERROR = 32;
    const ERRNO_REQUEST_CODECENTER_IP_ERROR = 64;
    const ERRNO_REQUEST_IP_ERROR = 128;
    const ERRNO_REQUEST_HOST_ERROR = 256;
    const ERRNO_REQUEST_CODECENTER_EXPIRED = 512;
    const ERRNO_REQUEST_LOTTERY_ERROR = 1024;
    const ERRNO_REQUEST_ISSUE_ERROR = 2048;
    const ERRNO_GET_PROC_VALID = 32767;
    const ERRNO_GET_PROC_NOT_EXISTS = 32768;
    const ERRNO_GET_PROC_INVALID = 4096;
    const ERRNO_SET_PROC_VALID = 16383;
    const ERRNO_SET_PROC_NOT_EXISTS = 16384;
    const ERRNO_SET_PROC_INVALID = 16384;
    const ERRNO_SET_PROC_CODE_ERROR = 8192;

    /**
     * 资源名称
     * @var string
     */
    public static $resourceName = 'CodeCenter';
    public static $treeable = false;
    public static $sequencable = false;

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'name',
        'domain',
        'version',
        'ip',
        'set_url',
        'set_verify_url',
        'get_url',
        'revise_url',
        'revise_verify_url',
        'alarm_url',
        'alarm_verify_url',
        'customer_key',
    ];

    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [];

    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = false;
    protected $fillable = [
        'name',
        'domain',
        'version',
        'ip',
        'set_url',
        'set_verify_url',
        'get_url',
        'revise_url',
        'revise_verify_url',
        'alarm_url',
        'alarm_verify_url',
        'customer_key',
    ];

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|max:20',
        'domain' => 'required|url|max:50',
        'version' => 'required|integer|min:1|max:2',
        'ip' => 'required|max:100',
        'set_url' => 'required|max:200',
        'set_verify_url' => 'required|max:200',
        'revise_url' => 'max:200',
        'revise_verify_url' => 'max:200',
        'alarm_url' => 'max:200',
        'alarm_verify_url' => 'max:200',
        'get_url' => 'max:200',
        'customer_key' => 'required|size:32',
    ];

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'id' => 'asc'
    ];

    /**
     * The array of custom error messages.
     *
     * @var array
     */
    public static $customMessages = [];

    /**
     * get default code center
     * @return CodeCenter
     */
    public static function getDefaultCenter() {
        return self::where('default', '=', 1)->get()->first();
    }

    public static function getCodeCenterByKey($sKey) {
        return self::where('customer_key', '=', $sKey)->get()->first();
    }

    /**
     * get code center
     * @return CodeCenter 
     */
    public static function getCenter($iVersion = 1) {
        return self::where('version', '=', $iVersion)->get()->first();
    }

    public function getFormatedIpAttribute() {
        return explode(',', $this->attributes['ip']);
    }

    /**
     * 强制域名最后加斜杠，其他url首字母去除斜杠
     * @return boolean
     */
    public function beforeValidate() {
        if (isset($this->domain) && !empty($this->domain)) {
            $this->domain = str_finish($this->domain, '/');
        }
        if (isset($this->set_url) && !empty($this->set_url) && starts_with($this->set_url, '/')) {
            $this->set_url = substr($this->set_url, 1);
        }
        if (isset($this->set_verify_url) && !empty($this->set_verify_url) && starts_with($this->set_verify_url, '/')) {
            $this->set_verify_url = substr($this->set_verify_url, 1);
        }
        if (isset($this->revise_url) && !empty($this->revise_url) && starts_with($this->revise_url, '/')) {
            $this->revise_url = substr($this->revise_url, 1);
        }
        if (isset($this->revise_verify_url) && !empty($this->revise_verify_url) && starts_with($this->revise_verify_url, '/')) {
            $this->revise_verify_url = substr($this->revise_verify_url, 1);
        }
        if (isset($this->alarm_url) && !empty($this->alarm_url) && starts_with($this->alarm_url, '/')) {
            $this->alarm_url = substr($this->alarm_url, 1);
        }
        if (isset($this->alarm_verify_url) && !empty($this->alarm_verify_url) && starts_with($this->alarm_verify_url, '/')) {
            $this->alarm_verify_url = substr($this->alarm_verify_url, 1);
        }
        if (isset($this->get_url) && !empty($this->get_url) && starts_with($this->get_url, '/')) {
            $this->get_url = substr($this->get_url, 1);
        }
        return parent::beforeValidate();
    }

}
