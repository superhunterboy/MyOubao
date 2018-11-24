<?php
class DbTool {

    public static function getDbThreadId(){
        $link = DB::connection()->getPdo();
        $thread_id = $link->query('SELECT CONNECTION_ID() as cid')->fetch(PDO::FETCH_ASSOC)['cid'];
        return $thread_id;
    }

    public static function & getDbThreads(){
        $aInfo = DB::select('show processlist');
        $data = [];
        foreach($aInfo as $aObject){
            $data[] = $aObject->Id;
        }
//        pr($data);
        return $data;
    }

//    /**
//     * 从数据库服务器返回mysql线程ID列表
//     * @return array 包含mysql线程ID的一维数组
//     */
//    public function getMysqlThreadList(){
//        $aProcessList   = DB::connection()->select("show processlist");
//        $aProcessIdList = array();
//        foreach ($aProcessList as $val){
//            $aProcessIdList[] = $val->Id;
//        }
//        return $aProcessIdList;
//    }

}