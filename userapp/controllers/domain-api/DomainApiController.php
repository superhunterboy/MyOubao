<?php

class DomainApiController extends Controller {
    /**
     * 资源模型名称，初始化后转为模型实例
     * @var string|Illuminate\Database\Eloquent\Model
     */
    protected $modelName = 'Domain';

    //获取登陆域名
    public function getDomains() {
        $this->model = App::make($this->modelName);
        $oQuery = $this->model->get(['domain'])->take(10)->toJson();
        echo $oQuery;
        exit;
    }

    /**
     * 获取登陆器信息
     */
    public function getSoftwareInfo() {
        $SysConfig = App::make('SysConfig');
        $versionQuery = SysConfig::readValue('login_software_version');
        $urlQuery = SysConfig::readValue('login_software_download_url');
        $data = ['version' => $versionQuery,
            'url' => $urlQuery,
        ];
        echo( json_encode($data));
        exit;
    }

    public function getEncode(){
        $keyCode = 'youwocainengkan';
        $allowSearchLotteryId = array(11, 12, 20, 23,24);
        $key = Input::get('key');
        if($key != $keyCode)
            echo json_encode(['error_msg'=>'wrong key']);
        else {
            $id = Input::get('id');
            if (!is_null($id) && is_numeric($id)) {
                if (in_array($id, $allowSearchLotteryId)) {
                    $oIssue = new Issue();
                    $oIssueData = $oIssue->getIssueArrayForWinNum($id, 120);
                    $issueData = [];
                    if ($oIssueData) {

                        foreach ($oIssueData as $iData) {
                            $issueData[] = ['issue' => $iData['number'], 'winNumber' => $iData['code']];
                        }
                    }
                    echo json_encode($issueData);
                } else {
                    echo json_encode(['error_msg' => 'wrong lotteryId']);
                }
            } else {
                echo json_encode(['error_msg' => 'not legal lottery_id']);
            }
        }
    }
}
