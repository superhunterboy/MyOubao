<?php

class PrizeLevel extends BaseModel {
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'prize_levels';
    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = false;
    protected $fillable = [
        'lottery_type_id',
        'basic_method_id',
        'level',
        'probability',
        'max_prize',
        'full_prize',
        'fixed_prize',
        'max_group',
        'min_water',
        'rule',
    ];

    public static $resourceName = 'Prize Level';
    /**
     * number字段配置
     * @var array
     */
    public static $htmlNumberColumns = [
        'max_prize' => 2
    ];

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'lottery_type_id',
        'basic_method_id',
        'level',
        'probability',
        'max_group',
        'full_prize',
        'fixed_prize',
        'max_prize',
        'min_water',
        'rule',
    ];

    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
//        'type' => 'aLotteryTypes',
        'basic_method_id' => 'aBasicMethods',
    ];

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'lottery_type_id' => 'asc',
        'basic_method_id' => 'asc',
    ];

    /**
     * the main param for index page
     * @var string
     */
    public static $mainParamColumn = 'basic_method_id';

    public $digitalCounts = [];
    public static $rules = [
        'basic_method_id' => 'required|integer',
        'level'           => 'required|numeric',
        'probability'     => 'required|numeric|max:0.9',
        'max_group'       => 'required|numeric',
        'full_prize'      => 'numeric',
        'fixed_prize'   => 'numeric',
        'max_prize'       => 'numeric',
        'min_water'       => 'numeric|max:0.1',
        'rule'            => 'max:50'
    ];

    protected function beforeValidate(){
        if (empty($this->basic_method_id)){
            return false;
        }
        $oBasicMethod          = BasicMethod::find($this->basic_method_id);
        $this->lottery_type_id = $oBasicMethod->lottery_type;
        if ($this->probability){
            $this->full_prize = formatNumber(2 / $this->probability,4);
        }
//        pr(!$this->max_prize);
//        exit;
//        if (empty($this->max_prize)){
//            exit;
        $oSeries         = Series::find($oBasicMethod->series_id);
        $this->max_prize = $this->full_prize * ($this->max_group / $oSeries->classic_amount);
        $this->min_water = 1 - $this->max_prize / $this->full_prize;
        //        }
        return parent::beforeValidate();
    }

    public static function getTheoreticPrizeSets($iTypeId){
        $array = [];
        $aData = self::where('lottery_type_id' , '=', $iTypeId)->get(['basic_method_id','level','full_prize']);
        foreach($aData as $model){
            $array[$model->basic_method_id][$model->level] = $model->full_prize;
        }
        return $array;
    }

}