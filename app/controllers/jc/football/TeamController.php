<?php
namespace JcController;
/**
 * Created by PhpStorm.
 * User: endless
 * Date: 15-12-3
 * Time: 上午11:20
 */
class TeamController extends \AdminController
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
    protected $modelName = '\JcModel\JcTeam';
}