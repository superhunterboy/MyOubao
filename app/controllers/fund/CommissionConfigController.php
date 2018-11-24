<?php

class CommissionConfigController extends AdminBaseController {

    protected $modelName = 'CommissionConfig';

    protected function beforeRender() {
        parent::beforeRender();
        $this->setVars('aSeriesWays', SeriesWay::getTitleList());
    }


/*    public static $listColumnMaps = [
        'blance' => 'serial_number_short',
        'status'        => 'formatted_status',
        'bet_number'    => 'display_bet_number',
        'amount'        => 'amount_formatted',
        'finished_amount'        => 'finished_amount_formatted',
        'canceled_amount'        => 'canceled_amount_formatted',
    ];

    public static $viewColumnMaps = [
        'status'        => 'formatted_status',
        'amount'        => 'amount_formatted',
        'bet_number'    => 'display_bet_number',
        'finished_amount'        => 'finished_amount_formatted',
        'canceled_amount'        => 'canceled_amount_formatted',
    ];*/

}