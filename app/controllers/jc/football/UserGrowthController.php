<?php
namespace JcController;
use JcModel\JcLotteries;
use JcModel\JcMethodGroup;
use JcModel\JcUserGrowth;

/**
 * Created by PhpStorm.
 * User: endless
 * Date: 15-12-3
 * Time: 上午11:20
 */
class UserGrowthController extends \AdminController
{
    /**
     * 资源视图目录
     * @var string
     */
    protected $resourceView = 'default';


    /**
     * 资源模型名称
     * @var string|Illuminate\Database\Eloquent\Model
     */
    protected $modelName = '\JcModel\JcUserGrowth';
    public function __construct(){
        parent::__construct();
        switch ($this->action) {
            case 'index':
                break;
            case 'view':
            case 'edit':
                $sModelName = $this->modelName;
                $sModelName::$ignoreColumnsInEdit = array_merge($sModelName::$ignoreColumnsInEdit,['user_id', 'method_group_id']);
                break;
            case 'create':
                break;
        }
    }

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        switch ($this->action) {
            case 'index':
                break;
            case 'view':
            case 'edit':
                break;
            case 'create':
                break;
        }
        parent::beforeRender();
        $this->setVars('validMethodGroup', JcMethodGroup::getTitleList());
        $this->setVars('validLottery', JcLotteries::getTitleList());

    }

}