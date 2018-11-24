<?php
//dezend by http://www.yunlu99.com/ QQ:270656184
class MethodType extends BaseModel
{
	/**
     * The database table used by the model.
     *
     * @var string
     */
	protected $table = 'method_types';
	/**
     * 软删除
     * @var boolean
     */
	protected $softDelete = false;
	protected $fillable = array('lottery_type', 'name', 'attribute_code', 'wn_function', 'sequencing', 'digital_count', 'unique_count', 'max_repeat_time', 'min_repeat_time', 'shaped', 'shape');
	static public $resourceName = 'Method Type';
	static public $sequencable = false;
	/**
     * the columns for list page
     * @var array
     */
	static public $columnForList = array('id', 'lottery_type', 'name', 'attribute_code', 'wn_function', 'sequencing', 'digital_count', 'unique_count', 'max_repeat_time', 'min_repeat_time', 'shaped', 'shape');
	static public $titleColumn = 'name';
	/**
     * 下拉列表框字段配置
     * @var array
     */
	static public $htmlSelectColumns = array('lottery_type' => 'aLotteryTypes');
	/**
     * order by config
     * @var array
     */
	public $orderColumns = array('lottery_type' => 'asc', 'id' => 'asc');
	/**
     * the main param for index page
     * @var string
     */
	static public $mainParamColumn = 'lottery_type';
	public $digitalCounts = array();
	static public $rules = array('lottery_type' => 'required|integer', 'name' => 'required|max:10', 'attribute_code' => 'max:20', 'wn_function' => 'required|max:64', 'sequencing' => 'required|in:0,1');
	static public $validAttributeCodes = array('A', 'S', 'I', 'U', 'O');
	static public $validNums = array('A' => '0-4', 'S' => '0-3', 'D' => '0-1', 'O' => '0-9', 'B' => '0-1', 'U' => '0-27');

	protected function beforeValidate()
	{
		$this->sequencing || $this->sequencing = 0;
		$this->shaped || $this->shaped = 0;
		$this->digital_count || $this->digital_count = NULL;
		$this->unique_count || $this->unique_count = NULL;
		$this->max_repeat_time || $this->max_repeat_time = NULL;
		$this->min_repeat_time || $this->min_repeat_time = NULL;
		return parent::beforeValidate();
	}
}

?>
