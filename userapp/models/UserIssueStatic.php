<?php
class UserIssueStatic extends BaseModel{
    protected $table = 'issue_player_static';
    protected $fillable = [
        'user_id',
        'lottery_id',
        'issue',
        'bet_total',
        'created_at',
        'updated_at'
    ];
   
}
