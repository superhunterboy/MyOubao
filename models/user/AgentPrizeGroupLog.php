<?php
# 一代限额奖金组
class AgentPrizeGroupLog extends BaseModel {
        /**
     * 资源名称
     * @var string
     */
    protected $table = 'agent_prize_group_log';
    protected $fillable = [
        'id',
        'agent_id',
        'assign_agent_id',
        'prize_group',
        'plus_num',
        'subtract_num',
        'note',
        'updated_at',
        'created_at',
    ];
     public static $rules = [
        'note'   => 'between:0,200',
        'agent_id' => 'required|between:0,16',
//        'limit_num'      => 'required|integer',
    ];
}