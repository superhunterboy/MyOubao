<?php

# 用户角色管理

class AdminLogController extends AdminBaseController {

    protected $modelName = 'AdminLog';

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
    }

}
