<?php
namespace JcController;
use JcModel\ManJcOdds;

/**
 * Created by PhpStorm.
 * User: endless
 * Date: 15-12-10
 * Time: 下午2:49
 */
class OddsController extends \AdminController
{
    /**
     * 资源视图目录
     * @var string
     */
    protected $resourceView = 'jc.odds';


    /**
     * 资源模型名称
     * @var string|Illuminate\Database\Eloquent\Model
     */
    protected $modelName = '\JcModel\ManJcOdds';

    public function view($id){

        $oMatch = \JcModel\ManJcMatchesInfo::find($id);

        $match_id = $oMatch->match_id;

        $aOdds = \JcModel\ManJcOdds::getOddsByMatchId($match_id);

        $aOddses = [];
        if($aOdds->count()) {
            foreach ($aOdds as $oOdds) {
                $aOddses[$oOdds->method]['odds'][$oOdds->code] = $oOdds->odds;
                $aOddses[$oOdds->method]['code'][$oOdds->code] = $oOdds->name;
            }
        }
        $this->setVars('aOddses',$aOddses);
        return $this->render();

    }
    public function edit($id){
        if(\Request::method() == 'POST'){
            $bSucc = true;
            \DB::beginTransaction();
            //todo 若未抓到数据，需支持录入
            $oMatch = \JcModel\ManJcMatchesInfo::find($id);
            $iMatchId = $oMatch->match_id;
            foreach($_POST as $k => $v){
                if($k == '_token') continue;
                $bSucc = \JcModel\JcOdds::where('id','=',$k)
                        ->where('match_id', $iMatchId)
                        ->update(['odds'=>$v]);
            }
            if ($bSucc) {
                \DB::commit();
                \JcModel\ManJcOdds::deleteOddsDataCache($iMatchId);
                return $this->goBack('success', '修改成功！');
            } else {
                \DB::rollback();
                return $this->goBack('error', '修改失败！');
            }
        }else{
            $oMatch = \JcModel\ManJcMatchesInfo::find($id);
            $iLotteryId = $oMatch->lottery_id;
            $aMethods = \JcModel\JcMethod::getAllByLotteryId($iLotteryId);

            $match_id = $oMatch->match_id;

            $aOdds = \JcModel\ManJcOdds::getOddsByMatchId($match_id);
            $aOddses = [];
            if(!$aOdds->count()){
                foreach ($aMethods as $oMethod) {
                    $aCodes = explode(',',$oMethod->valid_nums);
                    foreach ($aCodes as $iCode) {
                        $oOdds = new ManJcOdds();
                        $oOdds->lottery_id = $oMethod->lottery_id;
                        $oOdds->match_id = $oMatch->match_id;
                        $oOdds->odds = 0;
                        $oOdds->method_id = $oMethod->id;
                        $oOdds->code = $iCode;
                        $oOdds->save();
                        $aOdds[] = $oOdds;
                    }
                }
            }

            foreach($aOdds as $oOdds){
                $oMethod = $aMethods[$oOdds->method_id];
                $aOddses[$oMethod->name][] = $oOdds;
            }

            $this->setVars('aOddses',$aOddses);
            return $this->render();
        }
    }

    private static function getInitOdds($type){
        $aOdds = [];
        switch($type){
            case 'crs' :
                $aOdds = ['-1-a'=>0,'-1-d'=>0,'-1-h'=>0];
                for($i = 0; $i <= 5; $i++){
                    for($j = 0; $j <= 5; $j++){
                        $aOdds['0'.$i.'0'.$j] = 0;
                    }
                }
                break;
            case 'had' :
            case 'hhad' :
                $aOdds = ['a'=>0,'d'=>0,'h'=>0];
                break;
            case 'hafu' :
                $aHad = ['a','d','h'];
                foreach ($aHad as $a) {
                    foreach ($aHad as $h) {
                        $aOdds[$a.$h] = 0;
                    }
                }
                break;
            case 'ttg' :
                for($i=0; $i <= 7; $i++){
                    $aOdds['s'.$i] = 0;
                }
                break;
        }

        return $aOdds;
    }
}