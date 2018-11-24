<?php
namespace JcController;
/**
 * Created by PhpStorm.
 * User: endless
 * Date: 15-12-11
 * Time: 上午11:59
 */
class MatchMethodController extends \AdminController
{
    /**
     * 资源视图目录
     * @var string
     */
    protected $resourceView = 'jc.matchMethod';


    /**
     * 资源模型名称
     * @var string|Illuminate\Database\Eloquent\Model
     */
    protected $modelName = '\JcModel\ManJcMatchMethod';

    protected function beforeRender() {
        parent::beforeRender();
    }
    public function methodControl(){
        //todo 若未抓到数据，需支持录入
        if(\Request::method() == 'POST'){
            $data = \Input::all();
            $id = \Input::get('id');
            $oMatch = \JcModel\ManJcMatchesInfo::find($id);

            $iMatchId = $oMatch->match_id;

            $aMatchMethod = \JcModel\ManJcMatchMethod::getByMatchId($iMatchId);

            $bSucc = true;
            \DB::beginTransaction();

            if(count($aMatchMethod) > 0) {
                foreach ($aMatchMethod as $oMatchMethod) {
                    if(isset($data['is_enable:'.$oMatchMethod->id])) {
                        $oMatchMethod->is_enable = 1;
                        if(isset($data['is_single:'.$oMatchMethod->id])) {
                            $oMatchMethod->is_single = 1;
                        }else{
                            $oMatchMethod->is_single = 0;
                        }
                    }else{
                        $oMatchMethod->is_enable = 0;
                        $oMatchMethod->is_single = 0;
                    }
                    $bSucc = $oMatchMethod->save();
                }
            }

            if($bSucc) {
                \DB::commit();
                return $this->goBack('success', '设置成功！');
            }else{
                \DB::rollback();
                return $this->goBack('error', '设置失败！');
            }

        }else{
            $id = \Input::get('id');
            $oMatch = \JcModel\ManJcMatchesInfo::find($id);

            $iMatchId = $oMatch->match_id;

            $aMatchMethod = \JcModel\ManJcMatchMethod::getByMatchId($iMatchId);
            if($aMatchMethod->count()){
                $this->setVars('aMatchMethod',$aMatchMethod);
                return $this->render();

            }
        }


    }
}