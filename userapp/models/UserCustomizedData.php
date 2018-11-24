<?php

class UserCustomizedData extends BaseModel
{
    protected $table = 'user_customized_data';
    protected $fillable = [
        'm_key',
        'm_value',
    ];
    protected $primaryKey = 'id';

    public $timestamps = false;
static   $messages=[
    'key.required'=>' :attribute 不能为空.',
    'regex'=>'数据格式错误',
    'alpha_dash'=>'数据格式错误.',
    'numeric'=>'数据格式错误',
    'max'=>'数据格式错误'
];


    /**检查数据合法性
     * @param $aPrams
     * @return bool
     */
    public function checkCache($aPrams,&$aReturnMsg)
    {
        $aReturnMsg = [
            'isSuccess' => 0,
            'type' => 'error',
            'data' => []
        ];

        $rules=['key' => 'required|regex:/^[a-zA-Z0-9.=&]+$/', 'data' => 'required|array'];
        //验证数据格式
        $validator = Validator::make($aPrams,$rules,self::$messages);
        if ($validator->fails()) {
            $aReturnMsg['msg'] =$validator->messages()->first();;
            return false;
            //数据格式错误
        }
        parse_str($aPrams['key'], $aPrams['key']);
        $aUserBehaviorList = Config::get('var.userBehaviorList');
        $validator_key = Validator::make($aPrams['key'], $aUserBehaviorList['keys'],self::$messages);
        if($validator_key->fails()){
            $aReturnMsg['msg'] =$validator_key->messages()->first();;
            return false;
        }

        $validator_value = Validator::make($aPrams['data'], $aUserBehaviorList['valuekeys'],self::$messages);
        if ($validator_value->fails() ) {
            $aReturnMsg['msg'] =$validator_value->messages()->first();
            return false;
        }
        return true;

    }

    /**保存缓存
     * @param $cacheKey
     * @param $jData
     * @return bool
     */
    public function saveCache($aData,&$aDatas)
    {

        $aDatas = [
            'isSuccess' => 1,
            'msg'       => '用户行为习惯保存成功',
            'type' => 'success',
            'data' => []
        ];
        $sCacheKey = self::getCacheKey($aData['key']);
        $jData = json_encode($aData['data']);
        $sAttributes = Cache::get($sCacheKey);
        if (!$sAttributes || json_encode($sAttributes) != $jData) {
            $oUserCustomizedData = self::where('m_key', $sCacheKey)->first();
            empty($oUserCustomizedData) ? $oUserCustomizedData = new UserCustomizedData() : '';
            $oUserCustomizedData->m_key = $sCacheKey;
            $oUserCustomizedData->m_value = $jData;

            if(!$oUserCustomizedData->save()){
                $aDatas['type']='error';
                $aDatas['isSuccess']=0;
                $aDatas['msg']='用户行为习惯保存失败';
            }else{
                Cache::forever($sCacheKey, $jData);
            }
//            echo Cache::get($sCacheKey);

        }
        return $aDatas;

    }


    /**生成cache的key
     * @param $key
     * @param $val
     * @return string
     */
    static function getCacheKey($key)
    {
        $sCacheKey = 'user_' . Session::get('user_id') . '_' . preg_replace('/=|&/', '_', $key);
        return $sCacheKey;
    }


}