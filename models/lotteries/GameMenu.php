<?php

class GameMenu extends BaseModel {

    public static $resourceName = 'GameMenu';
    protected $table = 'game_menus';
    public static $sequencable = true;

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'id',
        'title',
        'pid',
        'series_id',
        'lottery_id',
        'url',
        'type',
        'sequence',
        'created_at',
        'updated_at',
    ];
    protected $fillable = [
        'id',
        'pid',
        'title',
        'series_id',
        'lottery_id',
        'url',
        'type',
        'sequence',
    ];

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'sequence'=>'asc',
        'type' => 'desc',
        
    ];

    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        'pid' => 'aPGroup',
        'lottery_id' => 'aLotteries',
    ];

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = [
        'pid' => 'between:1,10',
        'series_id' => 'between:1,10',
        'lottery_id' => 'between:1,10',
        'title' => 'required',
        'type' => 'required|in:0,1',
    ];

    /**
     * title field
     * @var string
     */
    public static $titleColumn = 'name';

    public static function getGroups() {
        $aDatas = [0 => '--top--'];
        if ($oDatas = self::whereType('1')->get(['id', 'title'])) {
            foreach ($oDatas as $oData) {
                $aDatas[$oData->id] = $oData->title;
            }
        }
//        pr($aDatas);exit;
        return $aDatas;
    }

    /**
     * 获取该彩中最近的开奖号码
     * @param $iLotteryId     * @return bool
     */
    public static function getGameMenu($iOpen) {
        
        
        $OpenLotteries= Lottery::getAllLotteries($iOpen);
        $aOpenLotteryids=array_column( $OpenLotteries,'id');
        $oTopGameMenus = self::where('type', 1)->get();
//        pr($oTopGameMenus->toArray());
        $data = [];
        foreach ($oTopGameMenus as $oTopGameMenu) {

            if (empty($oTopGameMenu->pid)) {
                $data[$oTopGameMenu->id]['title'] = $oTopGameMenu->title;
               
            } else {
                $data[$oTopGameMenu->pid]['children'][$oTopGameMenu->id]['title'] = $oTopGameMenu->title;
                 if ($oGameMenus = self::getDataByPid($oTopGameMenu->id,$aOpenLotteryids)) {
           
                    $data[$oTopGameMenu->pid]['children'][$oTopGameMenu->id]['children'] = $oGameMenus->toArray();
                }
                  
            }
        }
//pr($data);
//        pr($aOpenLotteryids);exit;
        return $data;
    }

    private static function getDataByPid($pid,$aOpenLotteryids){
        
        return self::where('pid', $pid)
                ->whereIn('lottery_id',$aOpenLotteryids)
                ->orderBy('series_id','asc')
                ->orderBy('sequence','asc')
                ->get(['lottery_id','series_id','title']);
    }
}
