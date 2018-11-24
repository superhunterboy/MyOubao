<?php

class UserMessage extends MsgUser {

    protected $fillable = [];
    public static $mobileColumns = [
        'id',
        'is_readed',
         
        'msg_title',
        'type_id',
        'updated_at',
    ];
    public static $columnForList = [
        'msg_title',
        'type_id',
        'updated_at',
    ];

    public static function getUserUnreadMessagesNum() {
        $iUserId = Session::get('user_id');
        $iNum = self::where('receiver_id', '=', $iUserId)->where('is_readed','=',0)->where('is_deleted','=',0)->count();
        return $iNum;
    }
    public static function getUserLatest10UnreadMessages($iUserId =0){
        if(!$iUserId) return;
        $aMessages = self::where('receiver_id','=', $iUserId)->where('is_deleted','=',0)->orderBy('updated_at','desc')->take(6)->get(['msg_title','id','type_id','updated_at']);
        return $aMessages;
    }
}
