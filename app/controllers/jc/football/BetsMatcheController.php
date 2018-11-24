<?php
/**
 * Created by PhpStorm.
 * User: endless
 * Date: 15-11-26
 * Time: 上午11:36
 */

namespace JcController;


class BetsMatcheController extends \AdminController
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
    protected $modelName = '\JcModel\JcBetsMatch';

    protected function beforeRender() {
        parent::beforeRender();
        $this->setVars('aCoefficients', \Config::get('bet.coefficients'));
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