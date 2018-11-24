<?php

# 链接开户管理

class SecurityQuestionsController extends UserBaseController {

    protected $resourceView = 'centerUser.security';
    protected $modelName = 'SecurityUserAnswer';
    public $resourceName = '';

    public function beforeRender() {
        $aQuestions = SecurityQuestion::getAllQuestions();
        $this->setVars(compact('aQuestions'));
        parent::beforeRender();
    }

    public function index() {
        $oRes = SecurityUserAnswer::isSetSecurityQuestionByUserId(Session::get('user_id'));
//      return 333;
//        return parent::index();
        $this->setVars(compact('oRes'));
        return $this->render();
    }

    public function create($id = null) {

        return parent::create($id);
    }

    public function checkrule() {
        $aSafeQuestions = Input::get('safe-question');
        $aSafeAnswers = Input::get('safe-answer');
        if(!is_array($aSafeQuestions) || !is_array($aSafeAnswers)){
            return $this->goBack('error', __('参数异常', $this->langVars));
        }
//      pr($aSafeQuestions);exit();
        foreach ($aSafeQuestions as $k => $v) {
            if (empty($v)) {
                unset($aSafeQuestions[$k]);
            }
        }
        foreach ($aSafeAnswers as $k => $v) {
            if (empty($v)) {
                unset($aSafeAnswers[$k]);
            }
            if (!empty($aSafeAnswers[$k]) && mb_strlen($aSafeAnswers[$k]) > 10) {
                return $this->goBack('error', __('答案的长度不能超过10个字符', $this->langVars));
            }
        }
        if (empty($aSafeQuestions) || count($aSafeQuestions) != 3) {
            return $this->goBack('error', __('请选择问题', $this->langVars));
        }
        if (empty($aSafeAnswers) || count($aSafeAnswers) != 3) {
            return $this->goBack('error', __('安全口令答案不可为空，请重新设置！', $this->langVars));
        }
        if (array_unique($aSafeQuestions) != $aSafeQuestions) {
            return $this->goBack('error', __('安全口令问题不能重复，请重新设置！', $this->langVars));
        }

        $data = [];
        foreach ($aSafeQuestions as $k => $v) {
            $id = intval($v);
            $oCon = SecurityQuestion::getContentById($id);
            if (empty($oCon)) {
                return $this->goBack('error', __('请选择正确的问题', $this->langVars));
            }
            $data[$k]['content'] = $oCon->content;
            $data[$k]['answer'] = $aSafeAnswers[$k];
            $data[$k]['id'] = $oCon->id;
        }
        $this->setVars('data', $data);
        return $this->render();
    }

    public function savedata() {
        $oRes = SecurityUserAnswer::isSetSecurityQuestionByUserId(Session::get('user_id'));
        if (!empty($oRes)) {
            return $this->goBack('error', __('您已经设置安全口令', $this->langVars));
        }
        $aId = Input::get('id');
        $aAnswer = Input::get('answer');
        foreach ($aAnswer as $k => $v) {
            if (empty($v)) {
                unset($aAnswer[$k]);
            }
            if (!empty($aAnswer[$k]) && mb_strlen($aAnswer[$k]) > 10) {
                return $this->goBack('error', __('答案的长度不能超过10个字符', $this->langVars));
            }
        }
        if (empty($aAnswer) || count($aAnswer) != 3) {
            return $this->goBack('error', __('安全口令答案不可为空，请重新设置！', $this->langVars));
        }
        if (array_unique($aId) != $aId) {
            return $this->goBack('error', __('安全口令问题不能重复，请重新设置！', $this->langVars));
        }
        $data = [];
        foreach ($aId as $k => $v) {
            $id = intval($v);
            $oCon = SecurityQuestion::getContentById($id);
            if (empty($oCon)) {
                return $this->goBack('error', __('请选择正确的问题', $this->langVars));
            }
            $data[$k]['content'] = Hash::make($aAnswer[$k]);
            $data[$k]['question_id'] = $oCon->id;
            $data[$k]['user_id'] = Session::get('user_id');
        }
        $bSuc = SecurityUserAnswer::insertData($data);
        if (!$bSuc) {
            return $this->goBack('error', __('安全口令设置失败', $this->langVars));
        }
         return Redirect::to(route('security-questions.index'))->with('success', __('设置成功'));
    }

    public function checksecurityanswer() {
        $aCallbackData = SecurityUserAnswer::getCallbackSession();
        if (empty($aCallbackData)) {
            return Redirect::home()->with('error', __('参数异常', $this->langVars));
        }

        $iUserid = Session::get('user_id');
        $aAnswer = SecurityUserAnswer::getUserAnswerByUserId($iUserid);
        if (!is_array($aAnswer) || count($aAnswer) < 1) {
            return $this->goBack('error', __('您还没有设置安全口令', $this->langVars));
        }
        if (Request::getMethod() == 'POST') {
            if (empty(Input::get('answer'))) {
                return $this->goBack('error', __('答案不可为空', $this->langVars));
            }
            $oQuestion = SecurityQuestion::getIdByContent(Input::get('question'));
            foreach ($aAnswer as $k => $v) {
                if ($v['question_id'] == $oQuestion->id) {
                    $iAnswer = $v['content'];
                }
            }
            $iParamAnswer = Input::get('answer');
//            if ($iAnswer == Input::get('answer')) {
            if (Hash::check($iParamAnswer ,$iAnswer)) {
                SecurityFailTimes::updateFailTimesByUserId($iUserid);
                $sCallbackUrl = $aCallbackData['callback_url'];
                $aInputData = $aCallbackData['data'];
                return SecurityUserAnswer::callback($sCallbackUrl, $aInputData);
            } else {
                $oUser = SecurityFailTimes::isUserExistsByUserId($iUserid);
                if (empty($oUser)) {
                    $data['user_id'] = $iUserid;
                    $data['times'] = 1;
                    SecurityFailTimes::insertData($data);
                } else {
                    SecurityFailTimes::updateTimesByUserId($iUserid);
                }
                $oTimes = SecurityFailTimes::getTimesByUserId($iUserid);
//              pr($oTimes->times);exit();
                if ($oTimes->times >= 6) {
                    $oUser = User::find($iUserid);
                    $oUser->blockUser();
                    if ($sessionId = Session::get('user_id')){
                        UserOnline::offline($sessionId);
                    }
                    Session::flush();
                    return Redirect::route('home');
                }
                return $this->goBack('error', __("您已经输错{$oTimes->times}次，连续输错6次账户将会被冻结！", $this->langVars));
            }
        }else {
            $iId = array_rand($aAnswer, 1);
            $oQuestion = SecurityQuestion::getQuestionById($aAnswer[$iId]['question_id']);
            $this->setVars(compact('oQuestion'));
            return $this->render();
        }
    }

}
