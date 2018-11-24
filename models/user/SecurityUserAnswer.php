<?php

class SecurityUserAnswer extends BaseModel {
    protected $table = 'security_user_answers';
    protected $fillable = [
        'content',
        'sequence',
    ];
    
    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'content',
        'sequence',
    ];
    
    public static $rules = [
        'content'                      => 'required',
        'sequence'                      => '',
    ];
    
    public static function insertData($data){
        return self::insert($data);
    }
    
    public static function isSetSecurityQuestionByUserId($userid){
        if(empty($userid)){
            return false;
        }
        return self::where('user_id', $userid)->first();
    }
    
    public static function getUserAnswerByUserId($userid){
        if(empty($userid)){
            return false;
        }
        return self::where('user_id', $userid)->get()->toArray();
    }
    
    private static function getCacheKey(){
        return 'security_cache_data_' . Session::get('user_id');
    }
    
    public static function getCallbackSession(){
        $sCacheKey = self::getCacheKey();
        return Cache::get($sCacheKey);
    }
    
    public static function callback($sUrl, $aData = []){
        return Redirect::to($sUrl)->withInput($aData)->with('fromSecurity', 1);
    }

    public static function Redirect(){
        $aInputData = Input::all();
        $sCacheKey = self::getCacheKey();
        $aCallbackData = [
            'callback_url' => Request::url(),
            'referer' => Request::server('HTTP_REFERER'),
            'data' => $aInputData,
        ];
        Cache::put($sCacheKey, $aCallbackData, 600);
        return Redirect::to(route('security-questions.checksecurityanswer'));
    }
    
    public static function destroyCallbackSession(){
        $sCacheKey = self::getCacheKey();
        return Cache::forget($sCacheKey);
    }
    
    public static function checkSecurity(){
        $aCallbackData = self::getCallbackSession();
        if ($aCallbackData){
            return $aCallbackData['callback_url'] == Request::url();
        }
        return false;
    }
    
    public static function checkReferer(){
        return Request::header('referer') == route('security-questions.checksecurityanswer') && Session::get('fromSecurity');
    }
}
