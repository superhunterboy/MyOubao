<?php

class ActiveRedEnvelopeController extends AdminBaseController {

    /**
     * 资源模型名称
     * @var string
     */
    protected $modelName = 'ActiveRedEnvelope';
    protected $customViewPath = 'active-red-envelopes';
    protected $customViews = [
        'generate',
        'setWays',
        'batchDelete',
        'configSettings',
    ];

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();
        $sModelName = $this->modelName;
        $aStatus = ActiveRedEnvelope::$aStatus;
        $this->setVars(compact('aStatus'));
        switch ($this->action) {
            case 'index':
            case 'edit':
                break;
            case 'create':
                break;
        }
    }

    public function generate() {
        $dBeginDate = SysConfig::readValue('active_red_envelopes_start_time');
        $dEndDate = SysConfig::readValue('active_red_envelopes_end_time');
        $time = SysConfig::readValue('active_red_envelopes_mins');
        $active_red_envelopes_amount = SysConfig::readValue('active_red_envelopes_amount');
        //检查开始时间和结束时间
        if (!$this->model->checkStartDate($dBeginDate) || !$this->model->checkEndDate($dEndDate)) {
            return $this->goBackToIndex('error', __('Generate failed!date error!'));
        }
        set_time_limit(0);
        $dBeginDate = strtotime($dBeginDate);
        $dEndDate = strtotime($dEndDate);
        $i = 0;
        $d = 0;
        while ($dBeginDate <= $dEndDate+1 - $time * 60) {
            if (!$d || date('d', $dBeginDate) != $d) {
                $i = 0;
                $d = date('d', $dBeginDate);
            }
            $i++;
            $sId = date('y', $dBeginDate) . date('m', $dBeginDate) . date('d', $dBeginDate) . date('H', $dBeginDate) . date('i', $dBeginDate);
            $start_time = date('Y-m-d H:i:s', $dBeginDate);
            $dBeginDate+=60 * $time;
            $end_time = date('Y-m-d H:i:s', $dBeginDate - 1);
            $data[] = array(
                'id' => $sId,
                'balance' => $active_red_envelopes_amount,
                'start_time' => $start_time,
                'end_time' => $end_time,
            );
        }
        if ($succ = $this->model->saveAll($data)) {
            return $this->goBackToIndex('success', __('Generate successful!'));
        }
        return $this->goBackToIndex('error', __('Generate failed!'));
    }

    public function setWays() {
        if (Request::method() == 'POST') {
            $lottery_ids = $this->params['lottery_ids'];
            foreach ($lottery_ids as $lottery => $way) {
                if (is_array($way)) {
                    $lottery_wasy_ids[$lottery] = $way;
                }
            }
            //测试数据
            $data = [];
            $del_res = true;
            $succ = true;
            $del_lottery_res = true;
            DB::connection()->beginTransaction();
            $aLottery_ids = ActiveRedEnvelopeWay::groupBy('lottery_id')->get(['lottery_id'])->toArray();

            $del_lottery_ids = array_diff(array_column($aLottery_ids, 'lottery_id'), array_keys($lottery_wasy_ids));
            if (!empty($del_lottery_ids)) {
                $del_lottery_res = ActiveRedEnvelopeWay::whereIn('lottery_id', $del_lottery_ids)->delete();
            }
//                   pr($lottery_wasy_ids);exit;
            foreach ($lottery_wasy_ids as $lottery_id => $values) {
                //获取已保存的方法
                $ways = ActiveRedEnvelopeWay::where('lottery_id', $lottery_id)->get(['lottery_id', 'id', 'way_id'])->toArray();
                $ways = array_column($ways, 'way_id');
                //比较得出新增数据和就数据
                $add_way_ids = array_diff($values, $ways);

                $del_way_ids = array_diff($ways, $values);
                if (!empty($del_way_ids)) {
                    //删除取消的
                    if (!$del_res = ActiveRedEnvelopeWay::where('lottery_id', $lottery_id)->whereIn('way_id', $del_way_ids)->delete()) {
                        break;
                    }
                }
                foreach ($add_way_ids as $way_id) {
                    $data[] = ['lottery_id' => $lottery_id, 'way_id' => $way_id];
                }
            }
            if (!empty($data)) {
                //保存新增
                $ActiveRedEnvelopeWay = new ActiveRedEnvelopeWay();
                $succ = $ActiveRedEnvelopeWay->saveAll($data);
            }
            if ($del_res && $succ && $del_lottery_res) {
                DB::connection()->commit();
                return $this->goBack('success', __(' successful!'));
            } else {
                DB::connection()->rollback();
                return $this->goBack('error', __(' failed!'));
            }
        } else {
//        获取所有的ways
            $lotterys = Lottery::getAllLotteries(true);
//        $lottery_ids=array_column($lotterys,'id');
            foreach ($lotterys as $val) {
                $ways = LotteryWay::getLotteryWaysByLotteryId($val['id']);
                $lottery_ways[$val['id']]['name'] = $val['name'];
                $lottery_ways[$val['id']]['id'] = $val['id'];
                $lottery_ways[$val['id']]['ways'] = $ways;
            }

            $oChecked = ActiveRedEnvelopeWay::get();
            foreach ($oChecked as $checked) {
                $aChecked[$checked['lottery_id']][$checked['way_id']] = $checked['way_id'];
            }
            $readonly = 0;
            $this->setVars(compact('lottery_ways', 'readonly', 'aChecked'));
            return $this->render();
        }
    }

    /**
     *
     */
    public function batchDelete() {
        if (Request::method() == 'POST') {

            $begin_id = $this->params['begin_id'];
            $end_id = $this->params['end_id'];
            if (empty($begin_id) || empty($end_id)) {
                return $this->goBack('error', __('begin_id adn end_id can not be null!'));
            }
            if (!$del_res = ActiveRedEnvelope::where('id', '>=', $begin_id)->where('id', '<=', $end_id)->delete()) {
                return $this->goBack('error', __(' failed!'));
            }
            return $this->goBackToIndex('success', __(' successful!'));
        } else {
            return $this->render();
        }
    }

    /**
     * 配置
     * @return type
     */

    public function configSettings(){
//echo 123;exit;
        $active_red_envelopes_start_time = SysConfig::readValue('active_red_envelopes_start_time');
        $active_red_envelopes_end_time = SysConfig::readValue('active_red_envelopes_end_time');
        $active_red_envelopes_mins = SysConfig::readValue('active_red_envelopes_mins');
        $active_red_envelopes_name= SysConfig::readValue('active_red_envelopes_name');

        $active_red_envelopes_amount = SysConfig::readValue('active_red_envelopes_amount');

        $active_red_envelopes_status = SysConfig::readValue('active_red_envelopes_status');
        $this->setVars(compact('active_red_envelopes_start_time','active_red_envelopes_end_time','active_red_envelopes_mins','active_red_envelopes_amount','active_red_envelopes_status','active_red_envelopes_name'));



        if(Request::method() == 'POST'){
            $aData = Input::all();
            !isset($aData['active_red_envelopes_status'])?$aData['active_red_envelopes_status'] = 0:$aData['active_red_envelopes_status'] = 1;
            if(
                SysConfig::setValue('active_red_envelopes_start_time',$aData['active_red_envelopes_start_time']) &&
                SysConfig::setValue('active_red_envelopes_end_time',$aData['active_red_envelopes_end_time']) &&
                SysConfig::setValue('active_red_envelopes_mins',$aData['active_red_envelopes_mins']) &&
                SysConfig::setValue('active_red_envelopes_amount',$aData['active_red_envelopes_amount']) &&
                SysConfig::setValue('active_red_envelopes_status',$aData['active_red_envelopes_status'])&&
                SysConfig::setValue('active_red_envelopes_name',$aData['active_red_envelopes_name'])
            )
                return $this->goBack('success', '设置成功！');
            else
                return $this->goBack('error', '设置失败！');
        }
        $this->render();
    }

}
