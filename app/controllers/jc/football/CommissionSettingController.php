<?php
/**
 * Created by PhpStorm.
 * User: endless
 * Date: 15-11-26
 * Time: 上午11:36
 */

namespace JcController;
use AdminController;
use JcModel\ManJcBet;
use JcModel\ManJcCommissionUser;


class CommissionSettingController extends AdminController
{
    /**
     * 资源视图目录
     * @var string
     */
    protected $resourceView = 'jc.commission';


    /**
     * 资源模型名称
     * @var string|Illuminate\Database\Eloquent\Model
     */
    protected $modelName = '\JcModel\ManJcCommissionUser';

    public function setting($user_id){
        $single_rate = $multiple_rate =0;
        $oCommissionSetting = ManJcCommissionUser::where('user_id','=',$user_id)->first();

        if($oCommissionSetting) {
            $single_rate =  $oCommissionSetting->single_rate;
            $multiple_rate = $oCommissionSetting->multiple_rate;
        }
        if(\Request::method() == 'POST') {
            $bSucc = true;
            $oUser = \User::find($user_id);

            if($oUser) {
                $parent_id = $oUser->parent_id;
                if($parent_id){
                    $oParentCommission = ManJcCommissionUser::where('user_id','=',$parent_id)->first();

                    if(!$oParentCommission) {
                        $parent_single_rate = $parent_multiple_rate = 0;
                    }else{
                        $parent_single_rate = $oParentCommission->single_rate;
                        $parent_multiple_rate = $oParentCommission->multiple_rate;
                    }

                    if(\Input::get('single_rate') > $parent_single_rate) {
                        return $this->goBack('error', '单关返点不能大于'.$parent_single_rate);
                    }

                    if(\Input::get('multiple_rate') > $parent_multiple_rate) {
                        return $this->goBack('error', '串关返点不能大于'.$parent_multiple_rate);
                    }
                }
            }
            if($oCommissionSetting){
                $bSucc = ManJcCommissionUser::where('user_id','=',$user_id)->update([
                    'single_rate' => \Input::get('single_rate'),
                    'multiple_rate' => \Input::get('multiple_rate')
                ]);
            }else{
                $bSucc = ManJcCommissionUser::insert([
                    'single_rate' => \Input::get('single_rate'),
                    'multiple_rate' => \Input::get('multiple_rate'),
                    'user_id' => $user_id
                ]);
            }

            if($bSucc) return $this->goBack('success', '设置成功！');
            else return $this->goBack('error', '设置失败！');
        }


        $this->setVars('single_rate', $single_rate);
        $this->setVars('multiple_rate', $multiple_rate);

        $this->render();
    }

}