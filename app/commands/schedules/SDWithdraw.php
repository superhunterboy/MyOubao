<?php

class SDWithdraw extends BaseTask {

    protected $a_do_list = null;

    protected function doCommand()
    {
        $a_processling_list = $this->data;
        // var_dump($this->data);
        $this->log = "--begin-request-a-withdraw-".  var_export($a_processling_list,true)."\r\n";
        $o_sd_order = new SdOrder();
        $return = $o_sd_order->_doWithdrawOrderCliProcess($a_processling_list);

        // echo "--------最终结果------\n";
        // var_dump($return);
        // echo "----------------------\n";
        $this->log = "--end-request-a-withdraw-\r\n";

        return $return;

        // return $return ? self::TASK_SUCCESS : self::TASK_KEEP;
    }

    public function api($data){
        $this->data = $data;
        $this->doCommand();
    }

}
