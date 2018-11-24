<?php

/**
 * Class ActivityFirstDepositCondition - 报名后的首充
 *
 * @author Johnny <Johnny@anvo.com>
 */
class ActivityFirstDepositCondition extends BaseActivityCondition {

    /**
     * 参数列表
     *
     * @var array
     */
    static protected $params = [
        'min_amount' => '最小充值额',
    ];

    /**
     * 条件是否满足
     *
     * @return bool
     */
    public function complete($userCondition) {
        $userTask = $userCondition->userTask()->first();

        if ($userTask) {
            $date = date('Y-m-d 00:00:00');

            $data = Deposit::where('status', '=', Deposit::DEPOSIT_STATUS_SUCCESS)
                    ->where('user_id', '=', $userCondition->user_id)
                    ->where('amount', '>=', $this->data->get('min_amount'))
                    ->where('updated_at', '>=', $date)
                    ->orderBy('updated_at', 'asc')
                    ->first();

            if (!empty($data)) {
                $userCondition->data = json_encode($data->getAttributes());
                $userTask->data = json_encode(['turnover' => $data->amount * 0.3, 'begin_time'=>$data->updated_at]);
                return true;
            }
        }

        return false;
    }

}
