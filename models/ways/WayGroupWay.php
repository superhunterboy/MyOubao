<?php

class WayGroupWay extends BaseModel {

    static protected $cacheLevel = self::CACHE_LEVEL_FIRST;
    protected $table = 'way_group_ways';

    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = false;
    protected $fillable = array('series_id', 'group_id', 'series_way_id', 'title', 'en_title', 'sequence', 'is_mobile');
    static public $resourceName = 'Way Group Way';
    static public $sequencable = true;

    /**
     * the columns for list page
     * @var array
     */
    static public $columnForList = array('series_id', 'group_id', 'title', 'en_title', 'series_way_id', 'sequence', 'is_mobile');

    /**
     * 下拉列表框字段配置
     * @var array
     */
    static public $htmlSelectColumns = array('series_id' => 'aSeries', 'group_id' => 'aWayGroups', 'series_way_id' => 'aSeriesWays');

    /**
     * order by config
     * @var array
     */
    public $orderColumns = array('sequence' => 'asc', 'id' => 'asc');

    /**
     * the main param for index page
     * @var string
     */
    static public $mainParamColumn = 'group_id';
    static public $rules = array('group_id' => 'required|integer', 'sequence' => 'integer', 'title' => 'max:20', 'en_title' => 'max:30', 'series_way_id' => 'required|integer', 'is_mobile' => 'in:0, 1');
    static public $treeable = false;

    protected function beforeValidate() {
        if ($this->group_id) {
            $oGroup = WayGroup::find($this->group_id);
            $this->series_id = $oGroup->series_id;
        }

        if (!$this->title && $this->series_way_id) {
            $oSeriesWay = SeriesWay::find($this->series_way_id);
            $this->title = $oSeriesWay->short_name;
        }

        parent::beforeValidate();
    }

}

?>