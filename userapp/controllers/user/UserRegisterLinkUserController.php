<?php
# 链接开户管理
class UserRegisterLinkUserController extends UserBaseController {
    protected $modelName = 'RegisterLinkUser';
    protected $resourceView = 'centerUser.linkUser';

    public function index()
    {
    	$iLinkId = trim(Input::get('register_link_id'));
    	// pr($iLinkId);
    	$oLink = UserRegisterLink::find($iLinkId);
    	// pr($oLink);exit;
    	// 确保用户只能查看自己创建的开户链接所开户的用户
    	if (! ($oLink->is_admin == 0 && $oLink->user_id == Session::get('user_id'))) {
    		$sMessage = __('_basic.no-rights');
    		// pr($sMessage);exit;
            // return $this->goBack('error', $sMessage);
            return Redirect::route('403');
    	}
    	return parent::index();
    }
}
