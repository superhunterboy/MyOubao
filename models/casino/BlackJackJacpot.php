<?php

class BlackJackJacpot extends BaseModel{
    protected $table = 'casino_jacpot';
    protected $jacpotPrize = 0;

    const BLACKJACK_UPDATE_JACPOT_ERROR=-701;


    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'max_prize',
        'prize',
        'proportion',
        'max_prize_rate',
    ];
    public static $listColumnMaps = [

    ];

    protected $fillable = [
        'max_prize',
        'prize',
        'proportion',

    ];
    public static $rules = [
        'max_prize'=>'required',
        'prize'=>'required',
        'proportion'=>'required',
        'max_prize_rate'=>'required',

    ];
    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'id' => 'asc'
    ];


    public function checkJacpotEnough($oTable,& $balancePrize=0,& $avaliblePrize=0){
        $uid = Session::get('user_id');
        $oUser = User::find($uid);
        if(!$oUser) return false;
        if($oUser->is_tester) return true;
        $iRequestPrize = $oTable->getRequestPrize();
        $sql = "select * from $this->table where id = ? for UPDATE ";
        $jacpotQuery = DB::select($sql,array($oTable->jacpot_id));
        $jacpotQuery = $jacpotQuery[0];
        $balancePrize = $jacpotQuery->prize;
        $this->jacpotPrize = $jacpotQuery->prize*$jacpotQuery->max_prize_rate;
        $avaliblePrize = min($this->jacpotPrize,$iRequestPrize);
        $result = ($this->jacpotPrize>=$iRequestPrize?true:false);

        $this->prize = $prize = $jacpotQuery->prize - $avaliblePrize;

        $updateResult=  DB::table($this->table)
            ->where('id', $jacpotQuery->id)
            ->update(['prize'=>$prize,'updated_at'=>date('Y-m-d H:i:s')]);

//        $queries = DB::getQueryLog();
//        $last_query = end($queries);

       // $updateResult = DB::table($this->table)->where('id',$oTable->jacpot_id)->update(['prize'=>$prize]);

        if(!$updateResult){

            return self::BLACKJACK_UPDATE_JACPOT_ERROR;
        }
        DB::connection()->commit();
        DB::connection()->beginTransaction();
        return (bool)$result;
    }

    static public function setJacpotWhenFinished($parent_project_id,$gameInfo){
        $uid = Session::get('user_id');
        $oUser = User::find($uid);
        if(!$oUser) return false;
        if($oUser->is_tester) return true;

        $projectDetail = BlackJackProjectDetail::where('parent_project_id',$parent_project_id)->get(['amount','prize','table_id']);
        $totalAmount = 0;
        $totalPrize = 0;
        $jacpot_id = null;
        $table_id = null;
        if(!$projectDetail) return false;
        foreach($projectDetail as $pDetail){
            $totalAmount += $pDetail->amount;
            $totalPrize += $pDetail->prize;
            if(is_null($jacpot_id) && is_null($table_id)){
                $jacpot_id = CasinoTable::find($pDetail->table_id)->jacpot_id;
                $table_id = $pDetail->table_id;
            }
        }
        if(is_null($jacpot_id)){
            return false;
        }
        $requestPrize = min($gameInfo['jacpotPrize'],$gameInfo['iRequestPrize']);
        $sql = "select * from casino_jacpot where id = ? for UPDATE ";
        //$oJacpot = DB::select($sql);
        $oJacpot = DB::select($sql,array($jacpot_id));
        $oJacpot = $oJacpot[0];
        $log = 'manProjectId:'.$parent_project_id;
        $log .= '  | EndBet     jacpotId:'.$jacpot_id.' | ';

        $log .= 'tableId:'.$table_id.' | ';
        $log .= 'totalAmount:'.$totalAmount.' | ';
        $log .= 'totalPrize:'.$totalPrize.' | ';
        $log .= '|requestPrize:'.$requestPrize.'|';
        $log .= 'jacpotPrize:'.$oJacpot->prize.' | ';



        if(($prizeBalance=$totalAmount-$totalPrize)>0){
            $jacpotPrize = $prizeBalance * $oJacpot->proportion+$requestPrize;
        }else{
            $jacpotPrize = $prizeBalance+$requestPrize;
        }


        $prize = $oJacpot->prize + $jacpotPrize;
        $prize = $prize>$oJacpot->max_prize?$oJacpot->max_prize:$prize;
        $log .= '| writePrize:'.$prize.'|';
        self::writeLog($log);
        $iResult = DB::table('casino_jacpot')->where('id',$jacpot_id)->update(['prize'=>$prize,'updated_at'=>date('Y-m-d H:i:s')]);
        return $iResult;
    }

    public function getJacpotPrize(){

          return $this->jacpotPrize;
    }
    static public  function writeLog($data)
    {

        $dir = '/tmp/blackjack/'.date('Y-m-d');
        $sFile = $dir.'/jacpot.log-'.date('H');;
        $arr = explode('/', $dir);
        $aimDir = '';
        foreach ($arr as $str) {
            $aimDir .= $str . '/';
            if (!file_exists($aimDir)) {
                mkdir($aimDir);
                @chmod($aimDir, 0777);
            }
        }
        if(!file_exists($sFile)){
            touch($sFile);
            chmod($sFile, 0777);
        }
        $bSucc = file_put_contents($sFile, '['.date('H:i:s').']  '.$data."\n",FILE_APPEND);

        return $bSucc;
    }
}