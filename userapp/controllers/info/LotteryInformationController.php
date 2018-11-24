<?php

class LotteryInformationController extends Controller {

    protected $resourceView = 'centerUser.lotteryinformation';
//    protected $resourceHelpView = 'help';
    protected $modelName = 'LotteryArticle';
    protected $iPageSize = 15;
    public function beforeRender() {
      
         $oLotteryInfoCate = LotteryCategory::getCategoryPid();
//         $this->setVars(compact('oLotteryInfoCate'));
         
        parent::beforeRender();
       
    }
    // 普通文章
    public function index($id = null, $name = null) {
        $datas=[];
        $oChildren = LotteryCategory::getChildrenById($id);
        $oChildren = LotteryCategory::getJcAndNumber();
        if(!empty($oChildren[0]) && $oChildren){
            foreach($oChildren as $k=>$oData){
                $aId[] = $oData->id;  
            }
            $id = intval($id);
            $LotteryArticle = new LotteryArticle();
            $oQuery =  LotteryArticle::where('status',LotteryArticle::STATUS_AUDITED)->where('category_id', $id);
            if (!$is_agent = Session::get('is_agent')) {
                $this->params['is_for_agent']=0;
                $oQuery=$oQuery->where('is_for_agent',0);
            }
           $oQuery= $LotteryArticle->doOrderBy($oQuery, ['created_at'=>'desc']);
            $datas = $oQuery->paginate($this->iPageSize);
        
        }
         if (!empty(Session::get('user_id'))) {
            if (Session::get('is_agent'))
               $aLatestAnnouncements = CmsArticle::getLatestRecords();
           else
               $aLatestAnnouncements = CmsArticle::getLatestRecords(7);

//            $unreadMessagesNum = UserMessage::getUserUnreadMessagesNum();
            $fAvailable = Account::getAccountInfoByUserId(Session::get('user_id'), ['available'])->available;
        }
//        
////        pr($datas);exit();
//        $this->setVars(compact('datas', 'name'));
//        return $this->render();
//       
         $unreadMessagesNum = UserMessage::getUserUnreadMessagesNum();
        $this->view = 'centerUser.lotteryinformation_agent.index';
        return View::make($this->view)->with(compact('unreadMessagesNum',  'name', 'datas', 'fAvailable', 'aLatestAnnouncements'));
    }
//
    public function view($id) {
        $oArticle = LotteryArticle::getArticlePidById($id);
        $oCateGory = LotteryArticle::getCateGoryById($oArticle->category_id);
        $data = LotteryArticle::where('id', $id)->first();
//        $this->setVars(compact('oCateGory'));
         $this->view = 'centerUser.lotteryinformation_agent.view';
          if (!empty(Session::get('user_id'))) {
            if (Session::get('is_agent'))
               $aLatestAnnouncements = CmsArticle::getLatestRecords();
           else
               $aLatestAnnouncements = CmsArticle::getLatestRecords(7);

//            $unreadMessagesNum = UserMessage::getUserUnreadMessagesNum();
            $fAvailable = Account::getAccountInfoByUserId(Session::get('user_id'), ['available'])->available;
        }
//        return parent::view($id);
          $unreadMessagesNum = UserMessage::getUserUnreadMessagesNum();
         return View::make($this->view)->with(compact('id', 'oCateGory', 'unreadMessagesNum', 'data', 'fAvailable', 'aLatestAnnouncements'));
    }

    //根据模板获取帮助中心类别列表
//    public function aIds() {
//        $aHelpTemlIds = CmsCategory::where('template', '=', 'help')->get(['id'])->toArray();
//        foreach ($aHelpTemlIds as $aId) {
//            $aIds[] = $aId['id'];
//        }
//        return $aIds;
//    }

    // // 帮助中心
    // public function helpIndex($iCategoryId = null, $iArticleId = null )
    // {
    //     $aCategories = CmsCategory::getHelpCenterCategories();
    //     $aArticles = CmsArticle::getHelpCenterArticles();
    //     // $aTitles = CmsArticle::getTitleList();
    //     $aTitles = [];
    //     // pr($aCategories->toArray());
    //     // pr($aArticles->toArray());
    //     // exit;
    //     foreach ($aCategories->toArray() as $key => $value) {
    //         if ($value['parent_id']) {
    //             $aTitles[$value['id']] = $value;
    //             if (! isset($aTitles[$value['id']]['children'])) {
    //                 $aTitles[$value['id']]['children'] = [];
    //             }
    //             foreach ($aArticles as $key2 => $aArticle) {
    //                 if ($aArticle['category_id'] == $value['id']) {
    //                     $aTitles[$value['id']]['children'][] = $aArticle->getAttributes();
    //                 }
    //             }
    //         }
    //     }
    //     if ($iCategoryId) {
    //        $datas = CmsArticle::getArticlesByCaregoryId($iCategoryId);
    //     } else {
    //         $datas = CmsArticle::getArticlesByCaregoryId(CmsCategory::HELP_ID);
    //     }
    //     $datas = $datas->toArray();
    //     // pr($CmsCategory::help_id);
    //     // pr($aTitles);
    //     // pr($datas);
    //     // exit;
    //     return View::make($this->resourceHelpView . '.helpIndex')->with(compact('aTitles', 'datas', 'iArticleId'));
    // }
}
