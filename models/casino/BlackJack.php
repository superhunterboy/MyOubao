<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 16-7-4
 * Time: 下午2:44
 */

class BlackJack extends CasinoBasic{
   CONST BLACKJACK_AUTO_HIT_SUCCESS=200;
   const BLACKJACK_BET_SUCCESS=400;
   const BLACKJACK_LOW_BALANCE=-403;
   const BLACKJACK_ENCODE_INIT_ERROR=-404;

   const BLACKJACK_VALIDATE_SUCCESS=-406;
   const BLACKJACK_AMOUNT_ERROR=-407;
   const BLACKJACK_DOUBLE_NUM_ERROR=-408;
   const BLACKJACK_STOPED=-409;
   const BLACKJACK_BET_ERROR = -410;
   const BLACKJACK_DOUBLE_BET_ERROR=-411;
   const BLACKJACK_CARD_NUM_ERROR=-412;
   const BLACKJACK_HIT_NOT_ENOUGH_COUNT=-413;
   const BLACKJACK_BANKER_CARD_ENNOUGH=-414;
   const BLACKJACK_INSURANCE_UNABLE=-415;
   const BLACKJACK_PLAER_CARD_STATUS_ERROR=-416;
   const BLACKJACK_CAL_ERROR=-417;
   const BlACKJACK_CREATE_PRO_ERROR=-418;
   const BACKJACK_INSURANCE_UNABLE=-419;

   const BLACKJACK_SP_SUCCESS=-500;
   const BLACKJACK_STAGE_ERROR=-505;
   const BLACKJACK_SP_CREATE_MANPRO_ERROR=-510;

   const BLACKJACK_LOW_BANLANCE=-511;
   const BLACKJACK_SYS_CONDITION=-512;
   const BLACKJACK_IN_GAME=-513;
   const BLACKJACK_BET_LIMIT_ERROR=-514;
   const BLACKJACK_REQUEST_WAY_ERROR=-515;









   const BLACKJACK_STATUS_DOUBLE_ABLE=0;
   const BLACKJACK_STATUS_DOUBLE_UNABLE=1;
   const BLACKJACK_STATUS_INSURANCE_ABLE=0;
   const BLACKJACK_STATUS_INSURANCE_UNABLE=1;
   const BLACKJACK_STATUS_SLIP_ABLE=0;
   const BLACKJACK_STATUS_SLIP_UNABLE=1;
   const BLACKJACK_STATUS_STOP=1;
   const BLACKJACK_STATUS_BUST=1;
   const BLACKJACK_STATUS_BLACKJACK=1;

   protected $aBetData = NULL;
   public $blackStage = NULL;
   public $gameInfo = NULL;
   protected $projectIds = array();
   protected $parent_project_id=NULL;
   protected $wayId  = NULL;
   static  $betAmountWays = array(BlackJackWay::BLACKJACK_WAY_PAIR,BlackJackWay::BLACKJACK_WAY_BET,BlackJackWay::BLACKJACK_WAY_INSURANT,BlackJackWay::BLACKJACK_WAY_SPLIT,BlackJackWay::BLACKJACK_WAY_DOUBLE);
   function __construct($oLottery, $oTable,$aBetdata)
   {
      parent::__construct($oLottery, $oTable);
      $this->aBetData = $aBetdata;
      $this->wayId = $aBetdata['wayId'];
      $this->blackStage = new BlackJackStage($oLottery,$oTable,$this->oUser);
      $this->oTable->initTable($this->aBetData);

   }



   public function getGameConfig(){
      return $ways = BlackJackWay::getWays($this->lottery_id);
   }
   protected function createProject(){


      $this->proInfos = $this->compileProject();
      foreach($this->proInfos as $proInfo){
         $oCasinoWay = BlackJackWay::find($proInfo['way_id']);
         $proInfo['method_id'] = $oCasinoWay->method_id;
         $oProject = new BlackJackProjectDetail($proInfo);
         $oProject->account_id = $this->oUser->account_id;
         $oProject->setAccount($this->oAccount);
         $oProject->setUser($this->oUser);
         $oProject->setLottery($this->oLottery);
         $iReturn = $oProject->addProject();
         if ($iReturn != Project::ERRNO_BET_SUCCESSFUL) {

            return $iReturn;
         }

      }
      return Project::ERRNO_BET_SUCCESSFUL;
   }

   public function startBet(){
      if($this->oTable->getAmount()<=0){
         return self::BLACKJACK_AMOUNT_ERROR;
      }
      if(!$this->oTable->checkAmount()){
         return self::BLACKJACK_BET_LIMIT_ERROR;
      }
      if ($this->oAccount->available < $this->oTable->getAmount()) {
         return self::BLACKJACK_LOW_BANLANCE;
      }
      $stageInfo = $this->blackStage->getStage();
      if($stageInfo){
         return self::BLACKJACK_IN_GAME;
      }

      $jacpotPrize = $avaliblePrize = 0;

         $jacpotEnough = $this->oJacpot->checkJacpotEnough($this->oTable,$jacpotPrize,$avaliblePrize);

      if(!is_bool($jacpotEnough)){
         return $jacpotEnough;
      }


      $encodeNums = $this->oEncode->initCards();

      if(!$encodeNums){
         return self::BLACKJACK_ENCODE_INIT_ERROR;
      }


      $oManProject = BlackJackProject::createProject($this->aBetData);

      if (empty($oManProject->id)) {

         return BlackJackProjectDetail::CASINO_BET_ERROR;
      }

      $this->oEncode->writeLog('man_project_id:'.$oManProject->id);
      $this->parent_project_id = $oManProject->id;
      if(!$this->oUser->is_tester){
         $log = 'manProjectId:'.$this->parent_project_id;
         $log .= '  | StartBet   jacpotId:'.$this->oTable->jacpot_id.' | ';
         $log .= 'tableId:'.$this->oTable->id.' | ';
         $log .= 'amount:'.$this->oTable->getAmount().' | ';
         $log .= 'tableRequest:'.$this->oTable->getRequestPrize().' | ';
         $log .= 'jacpotPrize:'.$jacpotPrize.'|';
         $log .= 'reduceMonyFromJacpot:'.$avaliblePrize.' | ';
         $this->oJacpot->writeLog($log);
      }

      $result = $this->createProject();
      if($result != Project::ERRNO_BET_SUCCESSFUL){

         return $result;
      }

//      $jacpotDetail = array(
//          'man_project_id'=>$this->parent_project_id,
//          'user_id'=>$this->oUser->id,
//          'request_table_id'=>$this->oTable->id,
//          'jacpot_id'=>$this->oJacpot->id,
//          'lottery_id'=>$this->oLottery->id,
//          'request_prize'=>$this->oTable->getRequestPrize(),
//          'available_prize'=>$this->oJacpot->getJacpotPrize(),
//          'balance'=>$this->oJacpot->prize,
//          'created_at'=>date('Y-m-d H:i:s'),
//      );
//      $oJacpotDetail = new BlackjackJacpotDetails($jacpotDetail);
//      $result = $oJacpotDetail->createDetails();
//      if(!$result){
//         return self::BlACKJACK_CREATE_PRO_ERROR;
//      }
      $jumpCards=[];
      $iReturn = $this->blackStage->initStage($this->aBetData,$jacpotEnough,$jumpCards);
      if(!empty($jumpCards)){
         $oCasinoWay = CasinoWay::find($this->wayId);
         foreach($jumpCards as $stageId => $jc){
            $log = 'ManProjectId:'.$this->parent_project_id.' | '.$stageId.'|'.$oCasinoWay->name . ' : '.implode(',',$jc);
            $this->oEncode->writeLog($log,'jumpCard');
         }

      }

      if($iReturn != BlackJackStage::BJ_STAGE_SUCCESS){

         return BlackJackProjectDetail::BLACKJACK_STAGE_ERROR;
      }else{
         $this->blackStage->compilePlayerStageData();
         $iStageInfo = $this->blackStage->stageInfo;

         $iStageInfo['gameInfo'] = array(
                                    'jacpotPrize'=>$this->oJacpot->getJacpotPrize(),
                                    'iRequestPrize'=>$this->oTable->getRequestPrize(),
                                    'jacpotEnough'=>$jacpotEnough?1:0,
                                    'manProjectId'=>$this->parent_project_id,
                                    );
         $this->gameInfo = $iStageInfo;
         $done = false;
         if($this->blackStage->check21($this->gameInfo['banker']['cards'])){
            $calPrize=$this->sendTask();
            $this->blackStage->delStage();
            foreach($this->gameInfo['player'] as $stageIndex => $stage){
               $this->gameInfo['player'][$stageIndex]['stop'] = self::BLACKJACK_STATUS_STOP;
            }
            $done = true;
         }else{
            $calPrize=$this->sendTask(array(BlackJackWay::BLACKJACK_WAY_PAIR));
         }

         if($calPrize){
            $saveResult = true;
            if(!$done)
               $saveResult = $this->blackStage->saveGameInfo($this->gameInfo);
            if($saveResult){
               return self::BLACKJACK_BET_SUCCESS;
            }else{

               return self::BLACKJACK_BET_ERROR;
            }
         }else{

            return self::BLACKJACK_CAL_ERROR;
         }
      }
   }

   public function hit($auto=NULL){
      $r = $this->validateStage();
      if($r != self::BLACKJACK_VALIDATE_SUCCESS){
         return $r;
      }
      $stageInfo = $this->getStage($stageId);

      if(!$stageInfo){
         return SELF::BLACKJACK_STAGE_ERROR;
      }
      $bankerCard = $this->gameInfo['banker']['cards'];
      if($this->blackStage->getCardsPoint($bankerCard[0]) == 11){
         if(!isset($this->gameInfo['insuranced'])){
            return self::BLACKJACK_BET_ERROR;
         }
      }
      if(($stageInfo['hitAbleCount']-=1)<0){
         return self::BLACKJACK_HIT_NOT_ENOUGH_COUNT;
      }
      if($stageInfo['bust'] == self::BLACKJACK_STATUS_BUST || $stageInfo['stop'] == self::BLACKJACK_STATUS_STOP){
         return self::BLACKJACK_STOPED;
      }

      $jacpotEnough = $this->gameInfo['gameInfo']['jacpotEnough'];
      $stageCards = explode(',',$stageInfo['cards']);
      if($auto == true && $this->blackStage->getCardsPoint($stageCards)>=17){
         $this->gameInfo['player'][$stageId]['stop'] = self::BLACKJACK_STATUS_STOP;
         $saveResult = $this->blackStage->saveGameInfo($this->gameInfo);
         return self::BLACKJACK_BET_SUCCESS;

      }


      if($jacpotEnough){
         $newCard = $this->oEncode->getCards();
      }else{
         $pos=0;
         $hasNum=true;
         $jumpCards = [];
         do{
            $jumpCards[] = $newCard = $this->oEncode->getCards($pos);
            if (empty($newCard)) {
               $hasNum = false;
               $jumpCards[] = $newCard = $this->oEncode->getCards();
               break;
            }
            $is21 = $this->blackStage->check21(array_merge($stageCards,array($newCard)));
            $pos++;

         }while($is21);
         if($hasNum){
            $del = $this->oEncode->delEncodeNum($newCard);
         }
         if(!empty($jumpCards)){
            $oCasinoWay = CasinoWay::find($this->wayId);
            $log = 'ManProjectId:'.$this->parent_project_id.' | '.$stageId.'|'.$oCasinoWay->name . ' : '.implode(',',$jumpCards);
            $this->oEncode->writeLog($log,'jumpCard');
         }
      }

      $stageCards = array_merge($stageCards,array($newCard));
      $this->gameInfo['player'][$stageId]['cards'] = implode(',',$stageCards);

      if($this->blackStage->checkBust($stageCards)){

         $this->gameInfo['player'][$stageId]['bust'] = self::BLACKJACK_STATUS_BUST;
         $this->gameInfo['player'][$stageId]['stop'] = self::BLACKJACK_STATUS_STOP;

      }
      $this->gameInfo['player'][$stageId]['hitAbleCount']--;
      if($this->gameInfo['player'][$stageId]['hitAbleCount']<=0 || $this->blackStage->check21($stageCards)){
         $this->gameInfo['player'][$stageId]['stop'] = self::BLACKJACK_STATUS_STOP;
      }


      $saveResult = $this->blackStage->saveGameInfo($this->gameInfo);
      if($saveResult){
         $this->gameInfo = array('wayId'=>$this->aBetData['wayId'],'newCard'=>$newCard,'stage'=>$this->gameInfo['player'][$stageId],'stageId'=>$stageId);

         return self::BLACKJACK_BET_SUCCESS;
      }else{

         return self::BLACKJACK_BET_ERROR;
      }
   }


   public function stop(){
      $this->validateStage();
      $stageInfo = $this->getStage($stageId);
      if(!$stageInfo){
         return SELF::BLACKJACK_STAGE_ERROR;
      }
     // $this->createProject();

      $this->gameInfo['player'][$stageId]['stop'] = self::BLACKJACK_STATUS_STOP;
      $saveResult = $this->blackStage->saveGameInfo($this->gameInfo);

      if($saveResult){

         return self::BLACKJACK_BET_SUCCESS;
      }else{

         return self::BLACKJACK_DOUBLE_BET_ERROR;
      }
   }

   public function split(){


      if ($this->oAccount->available < $this->oTable->getAmount()) {
         return self::BLACKJACK_LOW_BANLANCE;
      }
      $r = $this->validateStage();
      if($r != self::BLACKJACK_VALIDATE_SUCCESS){
         return $r;
      }
      $stageInfo = $this->getStage($stageId);
      if(!$stageInfo){

         return SELF::BLACKJACK_STAGE_ERROR;
      }

      if($stageInfo['stop'] == self::BLACKJACK_STATUS_STOP){
         return self::BLACKJACK_STOPED;
      }
      if($stageInfo['split_table_id']>0){

         return SELF::BLACKJACK_AMOUNT_ERROR;
      }

      $stageCards = explode(',',$stageInfo['cards']);
      if(count($stageCards) !=2 || ($this->blackStage->getCardsPoint($stageCards[0]) != $this->blackStage->getCardsPoint($stageCards[1]))){

         return SELF::BLACKJACK_CARD_NUM_ERROR;
      }

      $newStageId = $stageId+3;

      $newStageInfo = BlackJackStage::createSplitStage($stageCards[1],$this->aBetData['stage'][$stageId],array('father_table_id'=>$stageId));

      $newBetData = array(
                        'wayId' => $this->aBetdata['wayId'],
                        'tableId' => $this->aBetdata['tableId'],
                        'lotteryId' => $this->aBetData['lotteryId'],
                        'stage'=>array($newStageId=>$this->aBetData['stage'][$stageId]),
      );
      $this->aBetData = $newBetData;
      $result = $this->createProject();
      if($result != Project::ERRNO_BET_SUCCESSFUL){

         return $result;
      }
      $jacpotEnough = $this->gameInfo['gameInfo']['jacpotEnough'];
      $fatherStageCards = array(
          $stageCards[0],
      );
      if($jacpotEnough){
         $fatherStageCards[] = $this->oEncode->getCards();
      }else{
         $pos=0;
         $hasNum=true;
         $jumpCards = [];
         do{
            $jumpCards[] = $newCard = $this->oEncode->getCards($pos);
            if (empty($newCard)) {
               $hasNum = false;
               $jumpCards[] = $newCard = $this->oEncode->getCards();
               break;
            }
            $is21 = $this->blackStage->check21(array($stageCards[0],$newCard));
            $pos++;
         }while($is21);
         if($hasNum){
            $del = $this->oEncode->delEncodeNum($newCard);
         }
         $fatherStageCards[] = $newCard;
         if(!empty($jumpCards)){
            $oCasinoWay = CasinoWay::find($this->wayId);
            $log = 'ManProjectId:'.$this->parent_project_id.' | '.$stageId.'|'.$oCasinoWay->name . '  split : '.implode(',',$jumpCards);
            $this->oEncode->writeLog($log,'jumpCard');
         }
      }

      $this->gameInfo['player'][$stageId]['cards'] = implode(',',$fatherStageCards);

      if($this->blackStage->check21(explode(',',$this->gameInfo['player'][$stageId]['cards']))){
         $this->gameInfo['player'][$stageId]['stop'] = self::BLACKJACK_STATUS_DOUBLE_UNABLE;
         $this->gameInfo['player'][$stageId]['blackJack'] = self::BLACKJACK_STATUS_BLACKJACK;
      }
      //$this->gameInfo['player'][$stageId]['amount'][$this->wayId] = $amount;

      $this->gameInfo['player'][$stageId]['split_table_id'] = $newStageId;

      $this->gameInfo['player'][$newStageId] = $newStageInfo;
      //if($this->blackStage->getCardsPoint($stageCards[0]) == 11){
//         $this->gameInfo['player'][$stageId]['hitAbleCount']=1;
//         $this->gameInfo['player'][$newStageId]['hitAbleCount']=1;
    //  }

      $saveResult = $this->blackStage->saveGameInfo($this->gameInfo);
      if($saveResult){

         $this->gameInfo = array('wayId'=>$this->aBetdata['wayId'],'father_card'=>$fatherStageCards,'newCards'=>$newStageInfo,'newStageId'=>$newStageId);
         return self::BLACKJACK_BET_SUCCESS;
      }else{

         return self::BLACKJACK_DOUBLE_BET_ERROR;
      }

   }
   public function insurance(){

      $validateResult = $this->validateStage();
      if($validateResult != self::BLACKJACK_VALIDATE_SUCCESS){
         return $validateResult;
      }
      $totalAmount = 0;
      if(empty($this->aBetData['stage'])){
         foreach($this->gameInfo['player'] as $stageId => $info){
            $this->gameInfo['player'][$stageId]['insurance']=SELF::BLACKJACK_STATUS_INSURANCE_UNABLE;
         }

         $bankerNewCard='';
      }else{
         foreach($this->aBetData['stage'] as $stageId=>$wayAmount){
            if($this->gameInfo['player'][$stageId]['insurance']==SELF::BLACKJACK_STATUS_INSURANCE_UNABLE || ($this->blackStage->getCardsPoint($this->gameInfo['banker']['cards'][0]) != 11) ){

               return SELF::BLACKJACK_INSURANCE_UNABLE;
            }
         }
         foreach($this->aBetData['stage'] as $stageId=>$wayAmount){
            if(!isset($this->gameInfo['player'][$stageId])) continue;
            $stageInfo = $this->gameInfo['player'][$stageId];
            if(!$stageInfo){
               return SELF::BLACKJACK_STAGE_ERROR;
            }

            if( $stageInfo['insurance']==SELF::BLACKJACK_STATUS_INSURANCE_UNABLE ){

               return SELF::BACKJACK_INSURANCE_UNABLE;
            }
            $stageCards = explode(',',$stageInfo['cards']);
            if(count($stageCards)!=2 ){
               return SELF::BLACKJACK_CARD_NUM_ERROR;
            }
            if($stageInfo['stop'] == self::BLACKJACK_STATUS_STOP){
               return SELF::BLACKJACK_STOPED;
            }
            $this->aBetData['stage'][$stageId][BlackJackWay::BLACKJACK_WAY_INSURANT] =  $stageInfo['amount'][BlackJackWay::BLACKJACK_WAY_BET]/2;
            $amount = $stageInfo['amount'][BlackJackWay::BLACKJACK_WAY_BET]/2;
            $totalAmount+=$amount;
            $this->gameInfo['player'][$stageId]['insurance']=SELF::BLACKJACK_STATUS_INSURANCE_UNABLE;
            $this->gameInfo['player'][$stageId]['amount'][$this->wayId] = $amount;

         }
         if ($this->oAccount->available < $totalAmount) {
            return self::BLACKJACK_LOW_BANLANCE;
         }
         $result = $this->createProject();
         if($result != Project::ERRNO_BET_SUCCESSFUL){

            return $result;
         }
      }


      $bankerNewCard = $this->oEncode->getCards(0);
      if($this->blackStage->getCardsPoint($bankerNewCard) == 10){
         $this->oEncode->delEncodeNum($bankerNewCard);
         $this->gameInfo['banker']['cards'] = $newBankerCards = array_merge($this->gameInfo['banker']['cards'],array($bankerNewCard));
         foreach($this->gameInfo['player'] as $stage_id=>$stageInfo){
            $this->gameInfo['player'][$stage_id]['stop'] = SELF::BLACKJACK_STATUS_STOP;
         }
      }else{
         $bankerNewCard = '';
      }
      if(!empty($bankerNewCard)){
         $iResult = $this->sendTask();
         if(!$iResult){

            return self::BLACKJACK_CAL_ERROR;
         }else{
            $this->blackStage->delStage();
            return self::BLACKJACK_BET_SUCCESS;
         }
      }


      $this->gameInfo['insuranced'] = 1;
      $saveResult = $this->blackStage->saveGameInfo($this->gameInfo);
      if($saveResult){
         if($bankerNewCard==''){
            $this->gameInfo=array('wayId'=>$this->wayId,'bankerNewCard'=>'');
         }

         return self::BLACKJACK_BET_SUCCESS;
      }else{

         return self::BLACKJACK_BET_ERROR;
      }
   }

   private function sendTask($justWayId=array(),$justStageId=array()){
      $iResult =BlackJackProject::where('id',$this->parent_project_id)
          ->update(array('banker_number'=>implode(',',$this->gameInfo['banker']['cards'])));
      if($iResult === false){
        // return self::BLACKJACK_SP_CREATE_MANPRO_ERROR;
         return false;
      }

      $totalPrize=0;
      $totalAmount=0;

      foreach($this->gameInfo['player'] as $stageIndex =>$stageInfo){

         if(!empty($justStageId) && !in_array($stageIndex,$justStageId))
            continue;

         foreach($stageInfo['amount'] as $wayId => $amount){
            if((!empty($justWayId) && !in_array($wayId,$justWayId))   || !in_array($wayId,self::$betAmountWays))
               continue;


            $iResult = BlackJackProjectDetail::where('parent_project_id',$this->parent_project_id)
                         ->where('stage_id',$stageIndex)
                         ->where('way_id',$wayId)
                         ->where('status_prize',BlackJackProjectDetail::PRIZE_STAUTS_NORMAL)
                         ->where('status',BlackJackProjectDetail::STATUS_NORMAL)
                         ->update(array('player_number'=>$stageInfo['cards'],'status_prize'=>BlackJackProjectDetail::PRIZE_STATUS_WAITING));

            if($iResult === false){

               return true;
            }
            $iResult = $this->sendPrize($this->parent_project_id,$wayId,$stageIndex,$this->gameInfo,$totalPrize,$totalAmount);

            if(!$iResult){

               return false;
            }
         }
      }

      $oManProject = BlackJackProject::where('id',$this->parent_project_id)->where('status',BlackJackProject::STATUS_NORMAL)->get()->first();
      if(!$oManProject){
         return false;
      }
      $notFinishedCount = BlackJackProjectDetail::checkeAllProjectFinished($oManProject->id);

      if($notFinishedCount == 0){
         $iResult = $oManProject->setFinished(BlackJackProject::STATUS_FINISHE);
         if(!$iResult){
            return false;
         }else{

               $iResult = BlackJackJacpot::setJacpotWhenFinished($this->parent_project_id,$this->gameInfo['gameInfo']);

               return $iResult;

         }

      }else{
         return true;
      }


   }
   public function double(){
//      if($this->oTable->getAmount()<=0){
//         return self::BLACKJACK_AMOUNT_ERROR;
//      }

      $validateResult = $this->validateStage();
      if($validateResult != self::BLACKJACK_VALIDATE_SUCCESS){
         return $validateResult;
      }

      $stageInfo = $this->getStage($stageId);
      if(!$stageInfo){
         return SELF::BLACKJACK_STAGE_ERROR;
      }


      if( $stageInfo['double']==SELF::BLACKJACK_STATUS_DOUBLE_UNABLE){
         return SELF::BLACKJACK_DOUBLE_BET_ERROR;
      }
      $stageCards = explode(',',$stageInfo['cards']);
      if(count($stageCards)!=2 ){
         return SELF::BLACKJACK_CARD_NUM_ERROR;
      }
      if($stageInfo['stop'] == self::BLACKJACK_STATUS_STOP){
         return SELF::BLACKJACK_STOPED;
      }

      if($stageInfo['father_table_id'] !=0){
         $this->aBetData['stage'][$stageId][BlackJackWay::BLACKJACK_WAY_DOUBLE] =  $stageInfo['amount'][BlackJackWay::BLACKJACK_WAY_SPLIT];
         $amount = $stageInfo['amount'][BlackJackWay::BLACKJACK_WAY_SPLIT];
      }else{
         $this->aBetData['stage'][$stageId][BlackJackWay::BLACKJACK_WAY_DOUBLE] =  $stageInfo['amount'][BlackJackWay::BLACKJACK_WAY_BET];
         $amount = $stageInfo['amount'][BlackJackWay::BLACKJACK_WAY_BET];
      }
      if ($this->oAccount->available < $amount) {
         return self::BLACKJACK_LOW_BANLANCE;
      }

      $result = $this->createProject();
      if($result != Project::ERRNO_BET_SUCCESSFUL){

         return $result;
      }

      $this->gameInfo['player'][$stageId]['double']=self::BLACKJACK_STATUS_DOUBLE_UNABLE;
      $this->gameInfo['player'][$stageId]['amount'][$this->wayId] = $amount;

      $this->gameInfo['player'][$stageId]['hitAbleCount'] = 1;

      $saveResult = $this->blackStage->saveGameInfo($this->gameInfo);
      $this->hit();
      if($saveResult){

         //$this->gameInfo=array('wayId'=>$this->aBetdata['wayId']);
         return self::BLACKJACK_BET_SUCCESS;
      }else{
         return self::BLACKJACK_DOUBLE_BET_ERROR;
      }
   }


   public function bankerHit($auto=NUll){
      $validateResult = $this->validateStage();
      if($validateResult != self::BLACKJACK_VALIDATE_SUCCESS){
         return $validateResult;
      }

      $bankerInfo = $this->gameInfo['banker'];
      $bankerPoint = $this->blackStage->getCardsPoint($bankerInfo['cards']);
      if($bankerPoint <17){
         $tag = false;
         if($auto === true){
            $tag = true;
         }else{
            foreach($this->gameInfo['player'] as $stageId => $stageInfo){
               if(!$this->blackStage->checkBust(explode(',',$stageInfo['cards'])) && !$this->blackStage->checkBlackJack(explode(',',$stageInfo['cards'])) ){
                  $tag = true;
               }
               if($stageInfo['stop'] != SELF::BLACKJACK_STATUS_STOP){
                  return self::BLACKJACK_PLAER_CARD_STATUS_ERROR;
               }
            }
         }

   //      if( $bankerPoint = $this->blackStage->getCardsPoint($bankerInfo['cards']) > 17){
   //         $tag=false;
   //      }else if($bankerPoint == 17 ){
   //         if(count($bankerInfo['cards'])==2 && $this->blackStage->getCardsPoint($bankerInfo['cards'][0])!=11 &&  $this->blackStage->getCardsPoint($bankerInfo['cards'][1])!=11){
   //            $tag=false;
   //         }
   //      }
         $jacpotAvaliblePrize = min($this->gameInfo['gameInfo']['iRequestPrize'],$this->gameInfo['gameInfo']['jacpotPrize']);
         $minPrize = 0;
         $bestBankerCard = '';
         $getCodeIndex = 0;
         $jumpCards=[];
         while($tag){
            $newCard = $this->oEncode->getCards($getCodeIndex);
            if(!$newCard){

               break;
            }

            $newBankerCards = array_merge($bankerInfo['cards'],array($newCard));

            $bankerPoint = $this->blackStage->getCardsPoint($newBankerCards);
            if(count($newBankerCards)==2 && ($bankerPoint == 21)){
               $getCodeIndex++;
               continue;
            }

            if($bankerPoint<17 ||(count($newBankerCards)==2 && $bankerPoint==17 && ($this->blackStage->getCardsPoint($newBankerCards[0])==11 ||  $this->blackStage->getCardsPoint($newBankerCards[1])==11))){
               $bankerInfo['cards'] = $newBankerCards;
               $this->oEncode->delEncodeNum($newCard);
               $getCodeIndex=0;
               continue;
            }else{

               if(!$this->gameInfo['gameInfo']['jacpotEnough'] ){
                  $winPrize = 0;
                  $totalAmount = 0;
                  foreach($this->gameInfo['player'] as $stage_id => $stage_info){
                     $stageCards = explode(',',$stage_info['cards']);
                     $iRequest = $this->blackStage->compareCards($newBankerCards,$stageCards);

                     if($stage_info['bust']==0){
                        foreach($stage_info['amount'] as $wayId => $amount){
                           if(in_array($wayId,[BlackJackWay::BLACKJACK_WAY_DOUBLE,BlackJackWay::BLACKJACK_WAY_INSURANT,BlackJackWay::BLACKJACK_WAY_BET,BlackJackWay::BLACKJACK_WAY_PAIR])){
                              $oBlackJackWay = BlackJackWay::find($wayId);
                              $prize = $oBlackJackWay->{'prize'.ucfirst($oBlackJackWay->wn_function)}($stage_info['cards'],$amount,implode(',',$newBankerCards));
                              $winPrize += $prize;
                           }
                           if(in_array($wayId,[BlackJackWay::BLACKJACK_WAY_BET,BlackJackWay::BLACKJACK_WAY_PAIR])){
                              $totalAmount += $amount;
                           }
                        }
                     }
                  }

                  if($minPrize == 0 || $winPrize<$minPrize){
                     $minPrize = $winPrize;
                     $bestBankerCard = $newBankerCards;
                  }
                  $jumpCards[] = $newCard;
                  if($winPrize<=($jacpotAvaliblePrize+$totalAmount) || $winPrize==0){
                     $bestBankerCard = $newBankerCards;
                     break;
                  }

                  $getCodeIndex++;
               }else{
                  $bestBankerCard = $newBankerCards;

                  break;
               }
            }
         }
         if($tag === false){
            $getCodeIndex=0;
            while(true){
               $newCard = $this->oEncode->getCards($getCodeIndex);
               $bankerCard = array_merge($bankerInfo['cards'],array($newCard));
               $bankerPoint = $this->blackStage->getCardsPoint($bankerCard);
               if(count($bankerCard)==2 && ($bankerPoint == 21)){
                  $getCodeIndex++;
                  continue;
               }else{
                  $this->gameInfo['banker']['cards'] = $bankerCard;
                  $this->oEncode->delEncodeNum($newCard);
                  break;
               }
            }

         }
         if(!empty($jumpCards)){

            $oCasinoWay = CasinoWay::find($this->aBetData['wayId']);
            $log = 'ManProjectId:'.$this->gameInfo['gameInfo']['manProjectId'].' | '.$oCasinoWay->name . ' : '.implode(',',$jumpCards);

            $this->oEncode->writeLog($log,'jumpCard');
         }

         if($bestBankerCard != '') {
            $this->gameInfo['banker']['cards'] = $bestBankerCard;
            $this->oEncode->delEncodeNum($newCard);
         }


         $saveResult = $this->blackStage->saveGameInfo($this->gameInfo);
      }else{
         $saveResult = true;
      }
      if($saveResult){
         if($auto){
            $iResult = $this->sendTaskAuto();
         }else{
            $iResult = $this->sendTask();

         }

         if(!$iResult){
            return self::BLACKJACK_CAL_ERROR;
         }else{
            $this->blackStage->delStage();
            return self::BLACKJACK_BET_SUCCESS;
         }

      }else{
         return self::BLACKJACK_BET_ERROR;
      }

   }

   public function checkStatus(){
      $this->gameInfo = $this->blackStage->getStage()?$this->blackStage->getStage():[];

      return SELF::BLACKJACK_BET_SUCCESS;
   }
   public function validateStage(){
      $this->redis->watch($this->blackStage->redis_key);
      $encodeNums = $this->oEncode->getAllCards();
      if(!$encodeNums){
         return self::BLACKJACK_ENCODE_INIT_ERROR;
      }
      if(!$this->gameInfo = $this->blackStage->getStage()){

         return self::BLACKJACK_STAGE_ERROR;
      }
      foreach($this->aBetData['stage'] as $index => $stageWays){
         if(!empty($stageWays))
            foreach($stageWays as $wayId => $amount){
               if($wayId != $this->wayId){
                  return self::BLACKJACK_REQUEST_WAY_ERROR;
               }
            }
      }
      $this->parent_project_id = $this->gameInfo['gameInfo']['manProjectId'];
      $oManProject = BlackJackProject::find($this->parent_project_id);
      $oManProject->updated_at = date('Y-m-d H:i:s');
      $oManProject->update();
      return self::BLACKJACK_VALIDATE_SUCCESS;
   }

   public function compileProject(){
      $projectInfo = [];
      $iCount =0;
      $iDate = date('Y-m-d H:i:s');

      if(!empty($this->aBetData['stage'])) {
         foreach ($this->aBetData['stage'] as $stage_id => $stage) {
            foreach ($stage as $way_id => $amount) {
               if (empty($amount) || !in_array($way_id,self::$betAmountWays)) continue;
               $projectInfo[$iCount]['user_id'] = $this->oUser->id;
               $projectInfo[$iCount]['username'] = $this->oUser->username;
               $projectInfo[$iCount]['lottery_id'] = $this->oLottery->id;
               $projectInfo[$iCount]['table_id'] = $this->oTable->id;
               $projectInfo[$iCount]['stage_id'] = $stage_id;
               $projectInfo[$iCount]['account_id'] = $this->oUser->account_id;
               $projectInfo[$iCount]['parent_project_id'] = $this->parent_project_id;
               $projectInfo[$iCount]['way_id'] = $way_id;
               $projectInfo[$iCount]['amount'] = $amount;
               $projectInfo[$iCount]['bought_at'] = $iDate;
               $projectInfo[$iCount]['created_at'] = $iDate;
               $projectInfo[$iCount]['status'] = BlackJackProjectDetail::STATUS_NORMAL;
               $projectInfo[$iCount]['status_prize'] = BlackJackProjectDetail::PRIZE_STAUTS_NORMAL;
               $projectInfo[$iCount]['status_commission'] = BlackJackProjectDetail::COMMISSION_STATUS_WAITING;
               $projectInfo[$iCount]['serial_number'] = BlackJackProjectDetail::makeSeriesNumber($this->oUser->id);
               $projectInfo[$iCount]['is_tester'] = $this->oUser->is_tester;
               $iCount++;
            }
         }
      }
      return $projectInfo;
   }
   public function getStage(& $stageId){
      $stage = array_keys($this->aBetData['stage']);

      $stageId = isset($stage[0])?$stage[0]:0;

      $stageInfo = array();

      if(isset($this->gameInfo['player'][$stageId])){
         $stageInfo = $this->gameInfo['player'][$stageId];
      }
      return $stageInfo;
   }

   private function sendPrize($parent_project_id,$wayId,$stageId,&$gameInfo,&$totalPrize,&$totalAmount){

      $oCasinoWay = BlackJackWay::find($wayId);
      if($oCasinoWay->id == BlackJackWay::BLACKJACK_WAY_BET){
         if($gameInfo['player'][$stageId]['split_table_id']!=0){
            $oCasinoWay = BlackJackWay::find(BlackJackWay::BLACKJACK_WAY_SPLIT);
         }
      }
      $oManProject = BlackJackProject::where('id',$parent_project_id)->where('status',BlackJackProject::STATUS_NORMAL)->get();

      if(!$oManProject = $oManProject->first()){

         return true;
      }

      $pro = BlackJackProjectDetail::where('parent_project_id',$parent_project_id)
                                  ->where('stage_id',$stageId)
                                  ->where('way_id',$wayId)
                                  ->where('status',BlackJackProjectDetail::STATUS_NORMAL)
                                  ->where('status_prize',BlackJackProjectDetail::PRIZE_STATUS_WAITING)
                                  ->first();

      if(!$pro){
         return true;
      }

      $iResult = BlackJackProjectDetail::where('parent_project_id',$parent_project_id)
                                  ->where('stage_id',$stageId)
                                  ->where('way_id',$wayId)
                                    ->where('status',BlackJackProjectDetail::STATUS_NORMAL)
                                  ->where('status_prize',BlackJackProjectDetail::PRIZE_STATUS_WAITING)
                                  ->update(array('status_prize'=>BlackJackProjectDetail::PRIZE_STATUS_SENDING));
      if(!$iResult){

         return false;
      }

      $prize = $oCasinoWay->{'prize'.ucfirst($oCasinoWay->wn_function)}($pro->player_number,$pro->amount,$oManProject->banker_number);

      $totalPrize+=$prize;
      $totalAmount+=$pro->amount;
      $gameInfo['player'][$stageId]['prize'][$wayId] = $prize;
      $iReturn = Transaction::ERRNO_CREATE_SUCCESSFUL;
      if($prize>0) {
         $oAccount = Account::find($pro->account_id);
         $oUser = User::find($pro->user_id);
         $pro->setUser($oUser);
         $pro->setAccount($oAccount);


         $aExtraData = $pro->getAttributes();
         $aExtraData['project_id'] = $pro->id;
         $aExtraData['project_no'] = $pro->serial_number;
         $aExtraData['coefficient'] = 1.00;
         $aExtraData['issue'] = 0;

         unset($aExtraData['id']);
         $iReturn = Transaction::addTransaction($pro->User, $pro->Account, TransactionType::TYPE_SEND_PRIZE, $prize, $aExtraData);
      }

      if($iReturn == Transaction::ERRNO_CREATE_SUCCESSFUL){
         if($prize != 0){
            $update = array(
                'prize'=>$prize,
                'status'=>BlackJackProjectDetail::STATUS_WON,
                'status_prize'=>BlackJackProjectDetail::PRIZE_STATUS_SENT,
            );
         }else{
            $update = array(
                'prize'=>$prize,
                'status'=>BlackJackProjectDetail::STATUS_LOST,
                'status_prize'=>BlackJackProjectDetail::PRIZE_STATUS_SENT,
            );
         }

         $iReturn = $pro->where('id',$pro->id)->where('status_prize',BlackJackProjectDetail::PRIZE_STATUS_SENDING)->where('status',BlackJackProjectDetail::STATUS_NORMAL)->update($update);

         return $iReturn;
      }else{

         return false;
      }

      return true;
   }


   private function sendTaskAuto($justWayId=array(),$justStageId=array())
   {

      $iResult =BlackJackProject::where('id',$this->parent_project_id)
          ->update(array('banker_number'=>implode(',',$this->gameInfo['banker']['cards'])));
      if($iResult === false){
         return false;
      }
      foreach($this->gameInfo['player'] as $stageIndex =>$stageInfo){

         if(!empty($justStageId) && !in_array($stageIndex,$justStageId))
            continue;
         $ways = [];
         foreach($stageInfo['amount'] as $wayId => $amount){
            if((!empty($justWayId) && !in_array($wayId,$justWayId))   || !in_array($wayId,self::$betAmountWays))
               continue;
            $ways[] = $wayId;
         }
         if(empty($ways)) continue;
                  BlackJackProjectDetail::where('parent_project_id',$this->parent_project_id)
                      ->where('stage_id',$stageIndex)
                      ->whereIn('way_id',$ways)
                      ->where('status',BlackJackProjectDetail::STATUS_NORMAL)
                      ->where('status_prize',BlackJackProjectDetail::PRIZE_STAUTS_NORMAL)
                      ->update(array('player_number'=>$stageInfo['cards'],'status_prize'=>BlackJackProjectDetail::PRIZE_STATUS_WAITING));
      }
            $aJobData = array('parent_project_id'=>$this->parent_project_id,'lottery_id'=>$this->oLottery->id,'autoCalculate'=>1);


      $iResult = BaseTask::addTask('BjSendMoney', $aJobData, 'blackjack_send_money');
      return $iResult;
   }


}