<?php

class SpoController extends AuthorityController
{
    public function signup($sKeyword = null){
        $sUrl = $_SERVER['REQUEST_URI'];
        $hmcode = '';
        $need_cnzz = true;
        if($sUrl == '/zc'){
            $sKeyword = 'c5ad3505';
            $hmcode = 'd7efd135a4917da87e9fc07dafd31fa0';
        }elseif($sUrl == '/qm'){
            $sKeyword = 'b420deb3';
            $hmcode = '1c13ed7a8e850f07aa520e58088ea4cc';
        }elseif($sUrl == '/cg'){
            $sKeyword = '0d4b8c36';
            $hmcode = '19fabfa94a7a3f5081944fad92b64a9b';
        }elseif($sUrl == '/dg'){
            $sKeyword = '2ca96a67';
            $hmcode = '7b626ee0459cfb8ca784b792a0973816';
        }elseif($sUrl == '/sp'){
            $need_cnzz = false;
            $sKeyword = 'ec422c39';

            $hmcode = '45849872e58c911fd12a5cd29653d022';
        }elseif($sUrl == '/xiaomi'){
            $sKeyword = 'cba36223';
            $hmcode = '606edc6e977785e2ecb43ef062fe37bc';
        }

//        $sKeyword = $sKeyword? $sKeyword : trim(Input::get('prize'));

        if($sKeyword){
            if (! $oRegisterLink = UserRegisterLink::getRegisterLinkByPrizeKeyword($sKeyword)) {
                $oLink= UserRegisterLink::where('keyword', '=', $sKeyword)->first();
                $sTop=$oLink && $oLink->is_agent==1 && $oLink->is_admin==1?"总代客服":"您的上级代理";
                $sReason = '此开户链接已过期，请联系'.$sTop.'索取最新链接！';
                return Redirect::to('auth/signin')
                    ->withInput()
                    // ->withErrors(['attempt' => __('_basic.signup-fail')]);
                    ->with('error', $sReason);
            }
        }

        // pr($sKeyword);exit;
        if (Request::method() == 'POST') {
            return $this->postSignup();
        }
        // pr($sKeyword);exit;
        $oRegisterLink = null;
        $sViewFileName = 'spo.signup';


        if($sKeyword && $oRegisterLink = UserRegisterLink::getRegisterLinkByPrizeKeyword($sKeyword)){

            switch($sUrl){
                case '/cg':
                case '/dg':
                case '/sp':
                    $sViewFileName = 'spo.reg-cg-dg';
                    break;
                case '/zc':
                    $sViewFileName = 'spo.reg-a-u';
                    break;
                case '/qm':
                    $sViewFileName = 'spo.reg-xunisports';
                    break;
                case '/xiaomi':
                    $sViewFileName = 'spo.reg-xiaomi';
                    break;
                default:
                    $sViewFileName = 'spo.reg-a-u';
            }
        }

        return View::make($sViewFileName)->with(compact('sKeyword', 'oRegisterLink','hmcode','need_cnzz'));
    }
}
