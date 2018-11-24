<?php

/**
 * Class MsgSuggestionController 建议
 */
class MsgSuggestionController extends AdminBaseController
{
    /**
     * 资源模型名称，初始化后转为模型实例
     * @var string|Illuminate\Database\Eloquent\Model
     */
    protected $modelName = 'MsgSuggestion';

    /**
     * 开启建议
     *
     * @return RedirectResponse
     */
    public function open()
    {
        SysConfig::setValue('sys_use_suggestion', 1);

        return $this->goBackToIndex('success', __('_basic.updated', $this->langVars));
    }

    /**
     * 关闭建议
     *
     * @return RedirectResponse
     */
    public function close()
    {
        SysConfig::setValue('sys_use_suggestion', 0);

        return $this->goBackToIndex('success', __('_basic.updated', $this->langVars));
    }
}