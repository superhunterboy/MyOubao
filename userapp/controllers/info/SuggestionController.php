<?php
/**
 * Class SuggestionController
 * 客户建议
 *
 */
class SuggestionController extends UserBaseController
{
    protected $resourceView = 'centerUser.suggestion';

    protected $modelName = 'MsgSuggestion';

    const SUGGESTION_LIMIT_PER_DAY = 5;

    /**
     * 资源创建页面
     * @return Response
     */
    public function create($id = null)
    {

        //判断是否禁止提交
        if (SysConfig::readValue('sys_use_suggestion') != 1)
        {
            $this->halt(false, 'suggestion-deny', MsgSuggestion::ERRNO_SUGGESTION_NO_RIGHT);
        }

        if(mb_strlen(Input::get('comment')) > 1000){
            $this->halt(false, 'suggestion-size-overload', MsgSuggestion::ERRNO_SUGGESTION_SIZE_OVERLOAD);
        }
        $userId = Session::get('user_id');
        //每日每个用户不能超过5条记录
        $cacheKey    = "suggestion_key_{$userId}_".date('Y-m-d');

        if (($num = Cache::get($cacheKey)) == null)
        {
            $num  =   MsgSuggestion::findUserTodayMsg($userId)->count();

            //缓存24个小时数据
            Cache::put($cacheKey, intval($num), Carbon::now()->addMinutes(24*60));
        }

        if($num >= self::SUGGESTION_LIMIT_PER_DAY)
        {
            $this->halt(false, 'suggestion-overload', MsgSuggestion::ERRNO_SUGGESTION_OVERLOAD);
        }

        DB::connection()->beginTransaction();

        $data=[
            'user_id' => $userId,
            //'type'    => e(trim(Input::get('type'))),
            'type' => 0,
            'username' => Session::get('username'),
            'comment' => e(trim(Input::get('comment'))),
            'remark'  => e(trim(Input::get('remark'))),
            'ip'      => get_client_ip(),
        ];

        $this->model->fill($data);

        if ($this->model->save())
        {
            DB::connection()->commit();

            //计数器加1
            Cache::increment($cacheKey);
            $this->halt(true, 'suggestion-success', null);
        }
        else
        {
            DB::connection()->rollback();
            $this->halt(false, 'suggestion-submit-fail', MsgSuggestion::ERRNO_SUGGESTION_SUBMIT_FAILED);
        }
    }
}