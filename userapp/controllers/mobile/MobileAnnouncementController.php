<?php

class MobileAnnouncementController extends MobileBaseController {

    protected $modelName = 'CmsArticle';

    // 普通文章
    public function index() {
        $this->params['category_id'] = CmsArticle::TYPE_ANNOUMCEMENT;
        $data = parent::mobileIndex(CmsArticle::$mobileColumns);
        $this->halt(true, 'info', null, $a, $a, $data);
    }

    public function view($id) {
        $data = parent::view($id);
        $data = array_intersect_key($data, array_flip(array_merge(CmsArticle::$mobileColumns, ['content'])));
        $this->halt(true, 'info', null, $a, $a, $data);
    }

    //根据模板获取帮助中心类别列表
    public function aIds() {
        $aHelpTemlIds = CmsCategory::where('template', '=', 'help')->get(['id'])->toArray();
        foreach ($aHelpTemlIds as $aId) {
            $aIds[] = $aId['id'];
        }
        return $aIds;
    }

}
