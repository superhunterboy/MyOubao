<?php

/**
 * Class AgentApiController - 代理商相关接口
 *
 * 提供代理商相关的接口
 *
 * @author Johnny <Johnny@anvo.com>
 * @date 2014-11-28 10:42
 */
class AgentApiController extends Controller
{
    /**
     * 验证用户的登陆信息，返回用户的身份信息
     *
     * 参数列表：username=xxx&password=xxx
     *
     */
    public function verification()
    {
        $username = trim(Input::get('username'));
        $password = trim(Input::get('password'));

        $user = User::findUser($username);

        if (empty($user))
        {
            $this->endJsonMsg(-1, '该账户不存在');
        }

        if (!$user->is_agent)
        {
            $this->endJsonMsg(-3, '该账户不是代理商');
        }

        if (!empty($user->parent_id))
        {
            $this->endJsonMsg(-3, '该账户非总代');
        }

        if (Hash::check($password, $user->password))
        {
            $data = [
                'parent_id'=>$user->parent_id,
            ];

            $this->endJsonMsg(1, '校验成功', $data);
        }
        else
        {
            $this->endJsonMsg(-2, '密码错误');
        }
    }

    /**
     * 返回代理商的信息
     *
     *
     */
    public function info()
    {
        $users = User::where('is_agent', '=', 1)
                    ->where('parent_id', '=', null)
                    ->get();

        $data = [];
        foreach($users as $user)
        {
            $data[] = [
                'parent_id'=>$user->parent_id,
                'username'=>$user->username,
                'nickname'=>$user->nickname,
                'account'=>$user->account()->first()->available,
                'email'=>$user->email,
                'prize_group'=>$user->prize_group,
                'blocked'=>$user->blocked,
                'activated_at'=>$user->activated_at,
                'updated_at'=>$user->updated_at,
                'is_agent'=>$user->is_agent,
                'is_tester'=>$user->is_tester,
            ];
        }

        $this->endJsonMsg(1, '获取成功', $data);
    }

    /**
     * 终止请求，回给请求方信息
     *
     * @param $code
     * @param $msg
     * @param $data
     */
    private function endJsonMsg($code, $msg, $data=[])
    {
        $msgs = [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data,
        ];

        exit(json_encode($msgs));
    }
}