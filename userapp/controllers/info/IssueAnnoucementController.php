<?php

class IssueAnnoucementController extends Controller {

    protected $modelName = 'BaseModel';
    protected static $aIssueData = [
        "CQSSC" => [
            "count_issue_day" => 72,
            "encode_time" => '10分钟'
        ],
        "HLJSSC" => [
             "count_issue_day" => 84,
            "encode_time" => '10分钟'
        ],
         "XJSSC" => [
             "count_issue_day" => 96,
            "encode_time" => '10分钟'
        ],
         "TJSSC" => [
             "count_issue_day" => 84,
            "encode_time" => '10分钟'
        ],
          "JX11Y" => [
             "count_issue_day" => 78,
            "encode_time" => '10分钟'
        ],
        "GD11Y" => [
             "count_issue_day" => 84,
            "encode_time" => '10分钟'
        ],
           "SD11Y" => [
             "count_issue_day" => 78,
            "encode_time" => '10分钟'
        ],
        "F3D" => [
             "count_issue_day" => 1,
            "encode_time" => '每天一期'
        ],
          "PLW" => [
             "count_issue_day" => 1,
            "encode_time" => '每天一期'
        ],
         "JSK3" => [
             "count_issue_day" => 80,
            "encode_time" => '10分钟'
        ],
           "AHK3" => [
             "count_issue_day" => 80,
            "encode_time" => '10分钟'
        ],
           "BJPK10" => [
             "count_issue_day" => 179,
            "encode_time" => '5分钟'
        ]
    ];
    // 普通文章
    public function index() {
        $aLotteryId = [1, 3, 6, 7, 9, 8, 2, 14, 13, 21, 22, 53];
//        $oAllLottery = Lottery::getAllLotteryId();
        $oLottery  = Lottery::getLotteriesByLotteryIds($aLotteryId);
        $oIssue = new Issue;
        $IssueRule = new IssueRule;
        $aIssue = [];
        foreach ($oLottery as $k => $v) {
            $aGetIssue = $oIssue->getIssueArrayForWinNum($v->id, 1);
            if (!empty($aGetIssue)) {
                $aGetIssue[0]['id'] = $v->id;
                $aGetIssue[0]['name'] = $v->friendly_name;
                $aGetIssue[0]['count_issue_day'] = self::$aIssueData[$v->identifier]['count_issue_day'];       //每天总的期数
                $aCode = [];
                if (strpos($aGetIssue[0]['code'], ",") !== false) {
                    $aCode[] = explode(',', $aGetIssue[0]['code']);
                } elseif (strpos($aGetIssue[0]['code'], "|") !== false) {
                    $arr = [];
                    $arr = explode('|', $aGetIssue[0]['code']);
                    foreach ($arr as $k => $aWin) {
                        $arr1[] = explode(" ", $aWin);
                    }
                    foreach ($arr1 as $k => $data) {
                        foreach ($data as $j => $iNumber) {
                            $aCode[] = $iNumber;
                        }
                    }
                } elseif (strpos($aGetIssue[0]['code'], " ") !== false) {
                    $aCode[] = explode(' ', $aGetIssue[0]['code']);
                } else {
                    $aWinNumberLen = strlen($aGetIssue[0]['code']);
                    for ($i = 0; $i < $aWinNumberLen; $i++) {
                        $aCode[0][$i] = $aGetIssue[0]['code']{$i};        //开奖号码
                    }
                }
                $aGetIssue[0]['code'] = $aCode;
                $aGetIssue[0]['encode_time'] = self::$aIssueData[$v->identifier]['encode_time'];
                $aIssue[] = $aGetIssue[0];
            }
        }
//        pr($oLottery);exit();identifier
        if (!empty(Session::get('user_id'))) {
            if (Session::get('is_agent'))
               $aLatestAnnouncements = CmsArticle::getLatestRecords();
           else
               $aLatestAnnouncements = CmsArticle::getLatestRecords(7);

            $unreadMessagesNum = UserMessage::getUserUnreadMessagesNum();
            $fAvailable = Account::getAccountInfoByUserId(Session::get('user_id'), ['available'])->available;
        }
           $oJcLottery = \JcModel\JcLotteries::getByLotteryKey('football');
           $aMethodList = \JcModel\JcMethod::getAllByLotteryId($oJcLottery->id);
       $this->view = 'client.issueAnnoucement';
       return View::make($this->view)->with(compact('aIssue', 'aMethodList', 'aLatestAnnouncements', 'unreadMessagesNum', 'fAvailable'));
//        return $this->render();
    }
    
 
}
