<?php
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Support\Facades\Redis as Redis;


class BjAutoCalculateCommand extends BaseCommand {

    protected $sFileName = 'BjAutoCalculate';
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'blackjack:auto_calculate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'auto calculate blackjack stage.';




    public function fire()
    {



        !$this->writeTxtLog or $this->logFile = $this->logPath . DIRECTORY_SEPARATOR . $this->sFileName;
        $ago = date('Y-m-d H:i:s',time()-(15*60));
        $aConditions = array(
                            //'user_id'=>1,
                            'updated_at'=>['<',$ago],
                            'status'=>['=',BlackJackProject::STATUS_NORMAL],
                            );
       $aManProjects = BlackJackProject::getProjects($aConditions);
        $this->writeLog('['.date('Y-m-d H:i:s').'] begin BjAutoCalculateCommand ');



        foreach($aManProjects as $manProject){

            $this->writeLog('auto_cal:'.$manProject->id.' start');
            Session::set('user_id',$manProject->user_id);
            $oTable = BlackJackTable::find($manProject->table_id);
            $oLottery = CasinoLottery::find($manProject->lottery_id);
            $oUser = UserUser::find($manProject->user_id);
            $oBlackJackStage = new BlackJackStage($oLottery,$oTable,$oUser);
            $oEncode = new BlackJackEncode($oLottery,$oTable,$oUser);
            $allEncode = $oEncode->getAllCards();

            $gameInfo = $oBlackJackStage->getStage();

//            if(empty($gameInfo) || empty($allEncode)){
//                $gameInfo = json_decode($manProject->game_info,true);
//                $allEncode = $manProject->encode_cards;
//                pr($manProject->toArray());exit;
//                $encodes = [];
//                foreach($gameInfo['player'] as $stage){
//                    if(!empty($stage['cards'])){
//                        $cards =  explode(',',$stage['cards']);
//                        foreach($cards as $card){
//                            $encodes[] = $card;
//                        }
//                    }
//                }
//
//                foreach($gameInfo['banker']['cards'] as $card){
//                    $encodes[] =$card;
//                }
//
//                if(!empty($encodes)){
//                    $allEncode = explode(',',$allEncode);
//
//                    foreach($encodes as $code){
//                        foreach($allEncode as $index => $card){
//                            if($code == $card){
//                                unset($allEncode[$index]);
//                                break;
//                            }
//                        }
//                    }
//                }
//                $oEncode->saveEncodeCards($allEncode);
//                $oBlackJackStage->saveStage($gameInfo);
//            }





            if(isset($gameInfo['player'])){
                $oBlackJack = new BlackJack($oLottery,$oTable,array('stage'=>array(),'wayId'=>BlackJackWay::BLACKJACK_WAY_BANKERHIT));
                foreach($gameInfo['player'] as $stageId => $stageInfo){
                    if($stageInfo['stop'] == BlackJack::BLACKJACK_STATUS_STOP) continue;
                    $aBetData=array('stage'=>array($stageId=>[]),'wayId'=>BlackJackWay::BLACKJACK_WAY_HIT);
                    $oBlackJack = new BlackJack($oLottery,$oTable,$aBetData);
                    do{
                        $iReturn = $oBlackJack->hit(true);
                        $this->writeLog('player:' . $iReturn);
                    }while($iReturn == BlackJack::BLACKJACK_BET_SUCCESS);

                }

                $oBlackJack->blackStage->saveGameInfo($oBlackJack->gameInfo);
                $iReturn = $oBlackJack->bankerHit(true);
                if($iReturn == BlackJack::BLACKJACK_BET_SUCCESS) {
                    $this->writeLog('auto_cal:' . $manProject->id . ' success');
                }else{
                    $this->writeLog('auto_cal:'.$manProject->id.' fails '.$iReturn);
                }

            }else{

                $manProject->setFinished(BlackJackProject::STATUS_DROPED);

                $this->writeLog('auto_cal:'.$manProject->id.' not has gameInfo  be droped');
                continue;
            }



        }
        $this->writeLog('BjAutoCalculateCommand:done'."\n");
    }


}