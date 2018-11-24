<?php
  

class HelpController extends Controller {

    protected $resourceView = 'centerUser.announcement';
    protected $resourceHelpView = 'help';
    protected $modelName = 'CmsArticle';
    //普通文章
    // public function index(){
    //     $this->params['category_id'] = CmsArticle::TYPE_ANNOUMCEMENT;
    //     return parent::index();
    // }
    // public function view($id)
    // {
    //     return parent::view($id);
    // }

    // //根据模板获取帮助中心类别列表
    // public function aIds(){
    //      $aHelpTemlIds = CmsCategory::where('template', '=', 'help')->get(['id'])->toArray();
    //        foreach($aHelpTemlIds as $aId) {
    //             $aIds[] = $aId['id'];
    //        }
    //        return $aIds;
    // }

    //帮助中心
    public function helpIndex($iCategoryId = null, $iArticleId = null )
    {
        $aCategories = CmsCategory::getHelpCenterCategories();
        $aArticles = CmsArticle::getHelpCenterArticles();
        // $aTitles = CmsArticle::getTitleList();
        $aTitles = [];
        // pr($aCategories->toArray());
        // pr($aArticles->toArray());
        // exit;

        foreach ($aCategories->toArray() as $key => $value) {
            if ($value['parent_id']) {
                $aTitles[$value['id']] = $value;
                if (! isset($aTitles[$value['id']]['children'])) {
                    $aTitles[$value['id']]['children'] = [];
                }
                foreach ($aArticles as $key2 => $aArticle) {
                    if ($aArticle['category_id'] == $value['id']) {
                        $aTitles[$value['id']]['children'][] = $aArticle->getAttributes();
                    }
                }
            }
        }

        if ($iCategoryId) {
           $datas = CmsArticle::getArticlesByCaregoryId($iCategoryId);
        } else {
            $datas = CmsArticle::getArticlesByCaregoryId(CmsCategory::HELP_ID);
        }
        $datas = $datas->toArray();
        // pr($CmsCategory::help_id);
        // pr($aTitles);

        return View::make($this->resourceHelpView . '.helpIndex')->with(compact('aTitles', 'datas', 'iArticleId'));
    }

}
