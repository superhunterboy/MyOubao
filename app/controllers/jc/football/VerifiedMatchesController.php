<?php
namespace JcController;
/**
 * Created by PhpStorm.
 * User: endless
 * Date: 15-12-2
 * Time: 上午11:16
 */
use Illuminate\Support\Facades\Input;
use AdminController;
use Illuminate\Http\Request;
use BaseTask;
use JcModel\JcLeague;
use JcModel\JcLotteries;
use JcModel\JcTeam;
use JcModel\ManJcMatchesInfo;

class VerifiedMatchesController extends AdminController
{

    /**
     * 资源视图目录
     * @var string
     */
    protected $resourceView = 'jc.matchesinfo';


    /**
     * 资源模型名称
     * @var string|Illuminate\Database\Eloquent\Model
     */
    protected $modelName = '\JcModel\ManJcMatchesInfo';

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        $this->setVars('validLeagues', $this->getLeaguesList());
        $this->setVars('validHome', $this->getTeamsList());
        $this->setVars('validAway', $this->getTeamsList());
        $this->setVars('validLottery', JcLotteries::getTitleList());
        switch ($this->action) {
            case 'index':
                $this->setVars('aStatus', ManJcMatchesInfo::$validStatuses);
                $this->setVars('aHot', ManJcMatchesInfo::$validHot);
                break;
            case 'view':
            case 'edit':
            $this->setVars('validStatuses', ManJcMatchesInfo::$validStatuses);
            $this->setVars('validHot', ManJcMatchesInfo::$validHot);
                break;
            case 'create':
                $this->setVars('validStatuses', ManJcMatchesInfo::$validStatuses);
                break;
        }
    }

    public function verifyScore(){
        $id = Input::get('id');
        $oMatch = ManJcMatchesInfo::find($id);
        if($oMatch->status == ManJcMatchesInfo::MATCH_WAITING_STATUS_CODE){
            if(\Request::method() == 'POST'){
                if(Input::get('half_score') && Input::get('score')) $oMatch->status = ManJcMatchesInfo::MATCH_END_STATUS_CODE;
                else $oMatch->status = ManJcMatchesInfo::MATCH_CANCEL_STATUS_CODE;
                $oMatch->half_score = Input::get('half_score');
                $oMatch->score = Input::get('score');
                if($oMatch->save()){
                    $bSucc = $this->finishBetMatches($oMatch->lottery_id,$oMatch->match_id);
                }else{
                    $bSucc = false;
                }
                if($bSucc) return $this->goBack('success', '审核成功！');
                else return $this->goBack('error', '审核失败！');
            }
        }
        $this->setVars('oMatch',$oMatch);
        $this->setVars('half_score',$oMatch->half_score);
        $this->setVars('score',$oMatch->score);
        $this->setVars('id',$oMatch->id);
        $this->render();
    }
    public function batchVerifyScore(){
        $aWaitingMatches = ManJcMatchesInfo::getWaitingMatches();
        $bSucc = true;
        foreach ($aWaitingMatches as $oWaitingMatch) {
            if(!$oWaitingMatch->half_score && !$oWaitingMatch->score){
                $oWaitingMatch->status = ManJcMatchesInfo::MATCH_CANCEL_STATUS_CODE;
            }else{
                $oWaitingMatch->status = ManJcMatchesInfo::MATCH_END_STATUS_CODE;
            }
            $bSucc = $oWaitingMatch->save();

            if($bSucc) {
                $bSucc = $this->finishBetMatches($oWaitingMatch->lottery_id,$oWaitingMatch->match_id);
            }
        }

        if($bSucc) return $this->goBack('success', '批量审核赛果操作成功！');
        else return $this->goBack('error', '批量审核赛果失败！');
    }

    private function finishBetMatches($iLotteryId,$iMatchId){
        $aJobData = [
            'lottery_id' => $iLotteryId,
            'match_id' => $iMatchId,
        ];
        return BaseTask::addTask('\JcCommand\FinishBetMatches', $aJobData, 'jc_calculate');
    }
    private function getLeaguesList(){
        $aLeagues = JcLeague::getTitleList();
        return $aLeagues;
    }

    private function getTeamsList(){
        $aTeams = JcTeam::getTitleList();
        return $aTeams;
    }

}