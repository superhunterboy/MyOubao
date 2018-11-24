<?php
/**
 * Created by PhpStorm.
 * User: endless
 * Date: 15-11-26
 * Time: 上午11:36
 */

namespace JcController;
use JcModel\OriginalMatches;

class MatchesController extends \AdminController
{
    /**
     * 资源视图目录
     * @var string
     */
    protected $resourceView = 'jc.match';


    /**
     * 资源模型名称
     * @var string|Illuminate\Database\Eloquent\Model
     */
    protected $modelName = '\JcModel\OriginalMatches';

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        $this->setVars('aStatus', OriginalMatches::$validStatuses);
        $this->setVars('aHot', OriginalMatches::$validHot);
        switch ($this->action) {
            case 'index':
                break;
            case 'view':
            case 'edit':
                break;
            case 'create':
                break;
        }
    }

    public function matchesVerify($id=null){
        if(!$id)
            $id = \Input::get('id');
        if($id){
            $oSportMatch = OriginalMatches::find($id);
            if(time() > strtotime($oSportMatch->bet_end_time)){
                return $this->goBack('error', '赛事已过期！');
            }
            $iSucc = $this->_verifyMatch($oSportMatch);

            if($iSucc)
                return $this->goBack('success', '操作成功！');
            else
                return $this->goBack('error', '操作失败！');
        }
    }

    public function batchVerify(){
        $start_date = date('Y-m-d');
        $sMatchId = OriginalMatches::makeMatchId($start_date);
//        $end_date = date("Y-m-d",strtotime($start_date."+1 day"));  //加一天

        $oSportMatches = OriginalMatches::where('match_id','>=',$sMatchId)->where('status', OriginalMatches::MATCHE_STATUS_NEW)->get(['*']);
        if($oSportMatches->count()){
            foreach($oSportMatches as $oSportMatch){
                $this->_verifyMatch($oSportMatch);
            }
            return $this->goBack('success', '操作成功！');
        }else{
            return $this->goBack('error', '没有可以审核的赛事！');
        }
    }

    private function _verifyMatch($oSportMatch){
        if ($oSportMatch->status != OriginalMatches::MATCHE_STATUS_NEW){
            return false;
        }
        if(time() > strtotime($oSportMatch->bet_end_time)){
            return false;
        }
        \DB::beginTransaction();
        $isSucc = \JcModel\ManJcMatchesInfo::verifiedMatche($oSportMatch);
        if ($isSucc){
            $oSportMatch->status = OriginalMatches::MATCHE_STATUS_VERIFIED;
            $isSucc = $oSportMatch->save();
        }

        $isSucc ? \DB::commit() : \DB::rollback();
        return $isSucc;
    }
}