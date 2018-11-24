<?php

class PassiveRecordController extends AdminBaseController {
    /**
     * 资源视图目录
     * @var string
     */
    protected $resourceView = 'default';
//    protected $customViewPath = 'series.create-lottery';

    /**
     * 资源模型名称
     * @var string|Illuminate\Database\Eloquent\Model
     */
    protected $modelName = 'PassiveRecord';

}