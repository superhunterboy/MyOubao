<?php
class SeriesMethod extends BaseModel {

    public static $resourceName = 'Series Method';

    protected $table = 'series_methods';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
		'id',
		'series_id',
		'name',
        'basic_method_id',
        'offset',
        'hidden',
        'open',
    ];

    protected $fillable = [
		'series_id',
		'name',
        'basic_method_id',
        'offset',
        'hidden',
        'open',
    ];

    public static $htmlSelectColumns = [
        'series_id' => 'aSeries',
        'basic_method_id' => 'aBasicMethods',
    ];

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'id' => 'asc'
    ];

    /**
     * the main param for index page
     * @var string
     */
    public static $mainParamColumn = 'series_id';

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = [
        'series_id' => 'required|integer',
        'name'      => 'required|max:30',
        'basic_method_id' => 'required|integer',
        'offset' => 'required|numeric',
        'hidden' => 'in:0,1',
        'open' => 'in:0,1'
    ];

    /**
     * The array of custom error messages.
     *
     * @var array
     */
    public static $customMessages = [];

    /**
     * title field
     * @var string
     */
    public static $titleColumn = 'name';
    public $timestamps = false;

	/**
	 * 检查是否存在相同的游戏名称
	 *
	 * @return boolean
	 */
	private function _existName(){

    }

    /**
     * Write Cache
     */

    public function makeSeriesMethodCache(){
        // todo
    }

    public function getWinningNumber($sFullWinningNumber){
        $oBasicMethod   = BasicMethod::find($this->basic_method_id);
//        Log::info($this->name);
//        Log::info(var_export($this->attributes,1));
        $sWinningNumber = $oBasicMethod->getWnNumber($sFullWinningNumber,intval($this->offset));
//        $sWinningNumber = substr($sFullWinningNumber,intval($this->offset),$oBasicMethod->digital_count);
//        Log::info(var_export($this->attributes,1));
//        Log::info($sFullWinningNumber);
//        Log::info($sWinningNumber);
//        Log::info(var_export($oBasicMethod->attributes,1));
//        $a = $oBasicMethod->getWinningNumber($sWinningNumber);
//        Log::info('wn_number: ' . var_export($a,1));
        return $oBasicMethod->getWinningNumber($sWinningNumber);
    }

}
