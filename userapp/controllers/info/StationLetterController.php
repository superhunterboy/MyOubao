<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
# 站内信
class StationLetterController extends UserBaseController {
    protected $resourceView = 'centerUser.stationLetter';

    protected $modelName = 'UserMessage';

    protected function beforeRender() {
        parent::beforeRender();
        $aMsgTypes = MsgType::getMsgTypesByGroup(0);
        $this->setVars(compact('aMsgTypes'));
    }
    /**
     * [index 用户的站内信列表]
     * @return [Response] [description]
     */
    public function index(){
        $this->params['receiver_id'] = Session::get('user_id');
        return parent::index();
    }
    /**
     * [viewMessage 查看站内信详情, 相当于自定义view, 用户阅读后标记已读/未读状态, 并根据是否保持属性判断该条信息是否阅后即焚]
     * @param  [Integer] $id [站内信记录id]
     * @return [Response]    [description]
     */
    public function viewMessage($id)
    {
        $this->model = $this->model->find($id);
        if (!is_object($this->model)) {
            return $this->goBackToIndex('error', __('_basic.missing', $this->langVars));
        }
        // pr($this->model->msg_id);
        // pr(MsgMessage::find($this->model->msg_id)->content);
        // 只记录用户第一次阅读的时间
        if (!$this->model->readed_at && !$this->model->deleted_at) {
            $this->model->readed_at = date('Y-m-d H:i:m');
            $this->model->is_readed = 1;
            if (! $this->model->is_keep) {
                $this->model->deleted_at = date('Y-m-d H:i:m');
                $this->model->is_deleted = 1;
            }
        }
        $this->model->save([
            'readed_at' => MsgUser::$rules['readed_at'],
            'is_readed' => MsgUser::$rules['is_readed']
        ]);

        $oMsgMessage = MsgMessage::find($this->model->msg_id);
        // pr($oMsgMessage->exists);exit;
        if (!$oMsgMessage->exists) return $this->goBackToIndex('error', __('_basic.missing', $this->langVars));
        $this->model->msg_content = $oMsgMessage->content;
        $data = $this->model;
        $this->setVars(compact('data'));
        return $this->render();
    }
    /**
     * [getUserUnreadNum 获取用户未读信息的数量]
     * @return [Integer] [用户未读信息的数量]
     */
    public function getUserUnreadNum()
    {
        // TODO 测试html5的EventSource对象长连接
        // $response = new Symfony\Component\HttpFoundation\StreamedResponse(function() {
        //     $iOldNum = 0;
        //     while (true) {
        //         $iNewNum = UserMessage::getUserUnreadMessagesNum();
        //         if ($iNewNum != $iOldNum) {
        //             Log::info('test-event-source-' . time() . ': ' . $iNewNum);
        //             echo '{data: ' . ($iNewNum) . '}\n\n';
        //             ob_flush();
        //             flush();
        //         }
        //         sleep(30);
        //     }

        //     $iOldNum = $iNewNum;
        // });
        // $response->headers->set('Content-Type', 'text/event-stream');
        // $response->headers->set('Cache-Control', 'no-cache');
        // return $response;

        return UserMessage::getUserUnreadMessagesNum();
    }


}