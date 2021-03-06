<?php
namespace JcController;
use JcModel\JcLotteries;

/**
 * Created by PhpStorm.
 * User: endless
 * Date: 15-12-1
 * Time: 下午1:50
 */
class WayController extends \AdminController
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
    protected $modelName = '\JcModel\JcWay';

    protected function beforeRender() {
        parent::beforeRender();
        $this->setVars('validLotteries', JcLotteries::getTitleList());
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
}