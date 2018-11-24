<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

# 站内信

class MobileStationLetterController extends MobileBaseController {

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
    public function index() {
        $aPlusColumns = $this->params['receiver_id'] = Session::get('user_id');
        $data = parent::mobileIndex(UserMessage::$mobileColumns);
        $aMsgTypes = MsgType::getMsgTypesByGroup(0);
        $data['msg_type'] = $aMsgTypes;
        $this->halt(true, 'info', null, $a, $a, $data);
    }

    /**
     * [viewMessage 查看站内信详情, 相当于自定义view, 用户阅读后标记已读/未读状态, 并根据是否保持属性判断该条信息是否阅后即焚]
     * @param  [Integer] $id [站内信记录id]
     * @return [Response]    [description]
     */
    public function viewMessage($id) {
        $this->model = $this->model->find($id);
        if (!is_object($this->model)) {
            $this->halt(false, 'error', MsgMessage::ERRNO_MISSING_MESSAGE);
        }
        // 只记录用户第一次阅读的时间
        if (!$this->model->readed_at && !$this->model->deleted_at) {
            $this->model->readed_at = date('Y-m-d H:i:m');
            $this->model->is_readed = 1;
            if (!$this->model->is_keep) {
                $this->model->deleted_at = date('Y-m-d H:i:m');
                $this->model->is_deleted = 1;
            }
        }
        $this->model->save([
            'readed_at' => MsgUser::$rules['readed_at'],
            'is_readed' => MsgUser::$rules['is_readed']
        ]);

        $oMsgMessage = MsgMessage::find($this->model->msg_id);
        if (!$oMsgMessage->exists) {
            $this->halt(false, 'error', MsgMessage::ERRNO_MISSING_MESSAGE);
        }
        $data = array_intersect_key($this->model->toArray(), array_flip(UserMessage::$mobileColumns));
        $data['content'] = $oMsgMessage->content;
        $aMsgTypes = MsgType::getMsgTypesByGroup(0);
        $data['msg_type'] = $aMsgTypes;
        $data['unread_msg_count'] = UserMessage::getUserUnreadMessagesNum();
        $this->halt(true, 'info', null, $a, $a, $data);
    }

    /**
     * [getUserUnreadNum 获取用户未读信息的数量]
     * @return [Integer] [用户未读信息的数量]
     */
    public function getUserUnreadNum() {

        return UserMessage::getUserUnreadMessagesNum();
    }

}
