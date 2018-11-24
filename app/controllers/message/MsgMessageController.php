<?php
class MsgMessageController extends AdminBaseController
{
    /**
     * 资源视图目录
     * @var string
     */
    protected $resourceView = 'message';

    /**
     * 资源模型名称，初始化后转为模型实例
     * @var string|Illuminate\Database\Eloquent\Model
     */
    protected $modelName = 'MsgMessage';
    // protected $privateResourceView = 'message';

    protected function beforeRender()
    {
        parent::beforeRender();
        $aMsgTypes = MsgType::getMsgTypesByGroup(0);
        $this->setVars(compact('aMsgTypes'));
    }

    /**
     * [createMessage 自定义新建消息]
     * @param  [integer] $iReceiverType [接收者类型, 1: user, 2: agent, 3: all]
     */
    public function createMessage($iReceiverType = 1)
    {
        if (Request::method() == 'POST') {
            $this->params = trimArray(Input::except(['is_keep', 'all_children', 'direct_children', 'all_parent', 'not_self']));
            $aUsers       = $this->getReceivers($iReceiverType);
            if (!$aUsers) {
                $this->langVars['reason'] = __('_msg_messages.no-receivers');
                return $this->goBack('error', __('_basic.create-fail', $this->langVars));
            }
            $bSucc = $this->saveData();
            // pr((int)$bSucc);exit;
            if (!$bSucc) {
                $this->langVars['reason'] = & $this->model->getValidationErrorString();
                return $this->goBack('error', __('_basic.create-fail', $this->langVars));
            }
            // pr($aUsers);exit;
            $aParams    = $this->generateSyncParams( $aUsers );
            // pr($aParams);exit;
            if (!$aParams) return $this->goBack('error', __('_basic.create-fail', $this->langVars));
            // DB::connection()->beginTransaction();
            // if ($bSucc = $this->saveData($id)){
            //     DB::connection()->commit();
            //     return $this->goBackToIndex('success', __('_basic.created', $this->langVars));
            // }
            // else{
            //     DB::connection()->rollback();
            //     $this->langVars['reason'] = & $this->model->getValidationErrorString();
            //     return $this->goBack('error', __('_basic.create-fail', $this->langVars));
            // }
            // pr($aParams);
            // pr($this->model->users());
            // exit;
            return $this->saveMsgToUsers($aParams);
        } else {
            $this->setVars(compact('iReceiverType'));
            return $this->render();
        }
    }

    protected function saveMsgToUsers($aParams)
    {
        foreach ($aParams as $key => $value) {
            $this->model->users()->attach($key, $value);
        }
        $this->redictKey = 'curPage-MsgUser';
        return $this->goBackToIndex('success', __('_basic.created', $this->langVars));
    }
    /**
     * [getReceivers 生成接收者数组]
     * @param  [Integer] $iReceiverType [接收者类型]
     * @return [Array]                [接收者数组]
     */
    protected function getReceivers($iReceiverType)
    {
        // pr($iReceiverType);exit;
        // 1: assign user, 2: assign agent, 3: all
        switch ($iReceiverType) {
            case 1:
                $aUsers = $this->getReceiversByParam();
                break;
            case 2:
                $iAgentLevel = trim(Input::get('agent_level'));
                $aUsers = User::getAllUserNameArrayByUserType(1, $iAgentLevel);
                break;
            case 3:
                $aUsers = User::getAllUserNameArrayByUserType('all');
                break;
        }
        return $aUsers;
    }

    private function getReceiversByParam()
    {
        $bAllChildren    = trim(Input::get('all_children'));
        $bDirectChildren = trim(Input::get('direct_children'));
        $bAllParent      = trim(Input::get('all_parent'));
        $bNotSelf        = trim(Input::get('not_self'));

        $aUsernames = array_filter(preg_split("/[,; ]/", trim(Input::get('receiver')), -1, PREG_SPLIT_NO_EMPTY));
        // pr($bAllChildren);
        $aUsers = mapToArray( $this->getExtraUsersFromAgent($aUsernames), 'username');
        $aExtraUsers = [];
        $iAllOrDirectChildren = $bAllChildren ? 1 : ($bDirectChildren ? 2 : 0);
        if ($iAllOrDirectChildren) $aExtraUsers = $this->getExtraUsersFromAgent($aUsernames, $iAllOrDirectChildren);

        if ($bAllParent) {
            $aParents = $this->getExtraUsersFromAgent($aUsernames, 3);
            if (isset($aExtraUsers)) $aExtraUsers += $aParents;
        }
        if (!$bNotSelf) {

            $aUsers += $aExtraUsers;
        } else {
            $aInputUserIds = array_keys($aUsers);
            // pr(($aExtraUsers));
            // pr($aInputUserIds);
            if (isset($aExtraUsers) && count($aExtraUsers) > 0) {
                foreach ($aExtraUsers as $key => $value) {
                    if (in_array($key, $aInputUserIds)) array_forget($aExtraUsers, $key);
                }
            }
            // pr($aExtraUsers);exit;
            $aUsers = $aExtraUsers;
        }
        // pr($aUsers);exit;
        return $aUsers;
    }

    private function getExtraUsersFromAgent($aUsernames, $iType = null)
    {
        if (!$aUsernames) {
            $this->langVars['reason'] = '没有接收者';
            return [];
        }
        $aUsers = User::getUsersByUsernames($aUsernames);

        $iCount = User::getUsersByUsernames($aUsernames, true);
        // if ($iType) {
        //     pr($iCount);
        //     pr(count($aUsernames));
        //     exit;
        // }
        if ($iCount != count($aUsernames)) {
            $this->langVars['reason'] = '有部分接收者不存在';
            return [];
        }
        if (!isset($iType) || !$iType) {
            return $aUsers;
        }
        $aExtraUsers = [];
        // pr(count($aUsers));
        foreach ($aUsers as $oUser) {
            // pr($oUser->is_agent);exit;
            if (!$oUser->is_agent) continue;
            switch ($iType) {
                case 1:
                    $aExUsers    = $oUser->getUsersBelongsToAgent();
                    // foreach ($aExUsers as $key => $oObject) {
                    //     pr($oObject->id . '----' . $oObject->{'username'});exit;
                    // }
                    $aExUsers    = mapToArray($aExUsers, 'username');

                    $aExtraUsers += $aExUsers;
                    break;
                case 2:
                    $oUser = User::find($oUser->id);
                    $aExUsers    = mapToArray($oUser->children()->get(['id', 'username']), 'username');
                    // pr($aExUsers);
                    $aExtraUsers += $aExUsers;
                    break;
                case 3:
                    $oUser = User::find($oUser->id);
                    $aParentIds  = explode(',', $oUser->forefather_ids);
                    $aExUsers    = mapToArray($oUser->getUsersByIds($aParentIds), 'username');
                    $aExtraUsers += $aExUsers;
                    break;
            }
        }
        // pr($aExtraUsers);exit;
        // $aUsers = array_merge($aUsers, $aExtraUsers);
        return $aExtraUsers;
    }

    protected function generateSyncParams( $aUsers )
    {
        $aParams   = [];
        $sender_id = Session::get('admin_user_id');
        $sender    = Session::get('admin_username');
        $bIsKeep   = trim(Input::get('is_keep'));
        foreach ($aUsers as $sUserId => $sUsername) {
            $aParams[$sUserId] = [
                'receiver'  => $sUsername,
                'msg_title' => $this->model->title,
                'sender_id' => $sender_id,
                'sender'    => $sender,
                'type_id'   => $this->model->type_id,
                'is_keep'   => (int)$bIsKeep
            ];
        }
        return $aParams;
    }
}