<?php
//dezend by http://www.yunlu99.com/ QQ:270656184
class SeriesWayMethod extends BaseModel
{
	protected $table = 'series_way_methods';
	/**
     * 软删除
     * @var boolean
     */
	protected $softDelete = false;
	protected $fillable = array('series_id', 'name', 'single', 'basic_way_id', 'series_methods');
	static public $resourceName = 'Way Method Realation';
	/**
     * the columns for list page
     * @var array
     */
	static public $columnForList = array('name', 'single', 'basic_way_id', 'series_methods');
	/**
     * 下拉列表框字段配置
     * @var array
     */
	static public $htmlSelectColumns = array('series_id' => 'aSeries', 'series_methods' => 'aSeriesMethods', 'basic_way_id' => 'aBasicWays');
	static public $titleColumn = 'name';
	/**
     * order by config
     * @var array
     */
	public $orderColumns = array();
	/**
     * the main param for index page
     * @var string
     */
	static public $mainParamColumn = 'series_id';
	static public $rules = array('name' => 'required|max:30', 'series_id' => 'required|integer', 'basic_way_id' => 'required|integer', 'series_methods' => 'required|max:1024', 'single' => 'required|in:0,1');

	public function beforeValidate()
	{
		if (!isset($this->id)) {
			if (!$this->basic_way_id) {
				return false;
			}

			if (!$this->series_methods) {
				return false;
			}

			if (!$this->name) {
				$oBasicWay = BasicWay::find($this->basic_way_id);

				if (empty($oBasicWay)) {
					return false;
				}

				$this->name = $oBasicWay->name;
			}
		}

		return true;
	}
}

?>