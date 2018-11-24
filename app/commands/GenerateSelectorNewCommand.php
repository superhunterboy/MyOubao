<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class GenerateSelectorNewCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'admin-tool:selector-generate-new';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Generate selector json gor new game record.';


	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{





		//生成分类对应lottery_id文件
		$tag = array(1=>'lottery',2=>'casino',3=>'sport');

		$seriesSets = DB::table('series_sets')->select('type_id','series_ids')->get();
		$lotteryMap = array();
		foreach($seriesSets as $series){
			$seriesIds = explode(',',$series->series_ids);
			$lotterys = DB::table('lotteries')->select('id','series_id', 'name')->whereIn('status',[1,3])->wherein('series_id',$seriesIds)->get();
			if($lotterys) {
				foreach ($lotterys as $lo) {
					$lotteryMap[$tag[$series->type_id]][] = $lo->id;
				}
			}
		}
		$sportLottry = \JcModel\JcLotteries::getTitleList();

		$sportLottry = array_keys($sportLottry);
		$lotteryMap['sport'] = $sportLottry;

		$sResult = $this->_generateJsonFile($lotteryMap, 'lotterymap', 1, '');
		$this->line(json_encode($sResult));
		//配置
		$aWidgets  = Config::get('widget.widget');
		$aWidget = $aWidgets['lottery-series'];
		$sName        = isset($aWidget['name']) ? $aWidget['name'] : 'name';
		$sDataName   = isset($aWidget['dataName']) ? $aWidget['dataName'] : 'selectorData';
		$aColumns = array('id',$sName);
		if ($aWidget['extraParam']) $aColumns = array_merge($aColumns, $aWidget['extraParam']);
		//取得全部
		$oQuery    =  $aWidget['main']::whereRaw('open=1');
		$aMainData = $oQuery->get($aColumns);
//		$queries = DB::getQueryLog();
//		$last_query = end($queries);
//		pr($last_query);exit;
		$oLotteries = array();//彩票
		$oCasino = array();//电子娱乐场
		$oSport = array();//体育
		foreach($aMainData as $oMainData)
		{

			$aMainDataRecord = $oMainData->getAttributes();
			if($aWidget['friendly']) {
				$aMainDataRecord[$sName] = $oMainData->{$aWidget['friendly']};
			}
			if($aMainDataRecord['id']<=24 || $aMainDataRecord['id']==53 || $aMainDataRecord['id']==60 || ($aMainDataRecord['id'] >= 54 && $aMainDataRecord['id'] <= 59)) {
				$oLotteries[$aMainDataRecord['id']]=$aMainDataRecord;
			}
			else {
				$oCasino[$aMainDataRecord['id']] = $aMainDataRecord;
			}
		}
//

		unset($aMainData, $aChildData, $aColumns,$sportLottry);
		$lResult = $this->_generateJsonFile($oLotteries, 'lotterySelector', $sName, $sDataName);
		//$cResult = $this->_generateJsonFile($oCasino, 'test2', $sName, $sDataName);
		//$sResult = $this->_generateJsonFile($oSport, 'test3', $sName, $sDataName);
		$this->line(json_encode($lResult));
		//$this->line(json_encode($cResult));
		//$this->line(json_encode($sResult));
		//电子娱乐场分类json
		$seriesTypes = DB::table('series_sets')->select('id','series_ids', 'name')->where('type_id', '=', 2)->get();

		$seriesIds = array();
		if ($seriesTypes) {
			foreach ($seriesTypes as $se) {
				$seIds = explode(',',$se->series_ids);
				$seriesIds = array_merge($seriesIds,$seIds);
			}
		}


		$aMainData = $oQuery->get(array('id','series_id', 'name'));
		$fname = 'friendly_name';
		$lotterys=array();
		foreach($aMainData as $key => $oMainData)
		{
			$lotterys[$key] = $oMainData->getAttributes();
			$lotterys[$key]['name'] = $oMainData->{$fname};
		}

		$seriesSets = array();
		foreach($seriesTypes as $key=> $seType){
			$seriesSets[$seType->series_ids] = array(
				'id'=>$seType->series_ids,
				'name'=>$seType->name,
				//'series_id' =>  implode(',',$serid),
				'series_id' => $seType->series_ids,
					'open'		=> 1,
			);
		}

		$sResult = $this->_generateJsonFile($seriesSets, 'casinoSelector', $sName, $sDataName);
		$this->line(json_encode($sResult));
		//体育一级数据
		$sportLottry = \JcModel\JcLotteries::getTitleList();
		$oSport=array();
		if($sportLottry){
			foreach($sportLottry as $sId => $data ){
				$oSport[$sId] = array(
					'id' => $sId,
					'name'=> $data,
					'series_id' => $sId,
					'open' =>1
				);
			}
		}
		$sResult = $this->_generateJsonFile($oSport, 'sportSelector', $sName, $sDataName);
		$this->line(json_encode($sResult));
		//体育2及数据
		$data = \JcModel\JcMethodGroup::getAllBasic();
		$sportWay = array();

		foreach($data as $d){
			$sData= $d->getAttributes();
			$sportWay['series_id_'.$sData['lottery_id']]['id'] = $sData['lottery_id'];
			$sportWay['series_id_'.$sData['lottery_id']]['name'] = $sData['name'];
			$sportWay['series_id_'.$sData['lottery_id']]['children']['parent_id_'.$sData['id']]['id'] = $sData['id'];
			$sportWay['series_id_'.$sData['lottery_id']]['children']['parent_id_'.$sData['id']]['name'] = $sData['name'];
		}

		$sResult = $this->_generateJsonFile($sportWay, 'sportWay', $sName, 'selectorData');
		$this->line(json_encode($sResult));


		$generator = new SelectTemplateGenerator;
        $result = $generator->generate();
         $result = $generator->generateGroupWays(); // 专门用于彩种-玩法群-玩法三联下拉框数据的生成
        $this->line(json_encode($result));


		//21 blackjack level 1
		$blackjackLottery = CasinoLottery::getTitleList();
		$oBlackJack = array();
		if($blackjackLottery){
			foreach($blackjackLottery as $sId => $data){
				$oBlackJack[$sId] = array(
					'id' => $sId,
					'name' => $data,
					'series_id'=>$sId,
					'open' => 1,
				);
			}
		}
		$sResult = $this->_generateJsonFile($oBlackJack,'blackjackSelector',$sName,$sDataName);
		$this->line(json_encode($sResult));
		//21 blackjack level 2
		$blackjackMethods = CasinoMethod::all();
		$blackjackWay = array();
		foreach($blackjackMethods as $bjway){
			$sData = $bjway->getAttributes();
			$loId = 8001;
			$blackjackWay['series_id_'.$loId]['id'] = $loId;
			$blackjackWay['series_id_'.$loId]['name'] = $sData['name'];
			$blackjackWay['series_id_'.$loId]['children']['parent_id_'.$sData['id']]['id'] = $sData['id'];
			$blackjackWay['series_id_'.$loId]['children']['parent_id_'.$sData['id']]['name'] = $sData['name'];
			$subWay = BlackJackWay::where('method_id',$sData['id'])->get();


			foreach($subWay as $sw){
				$arr = ['id'=>$sw->id, 'name'=>$sw->name];
				$blackjackWay['series_id_'.$loId]['children']['parent_id_'.$sData['id']]['children'][]=$arr;
			}

		}

		$sResult = $this->_generateJsonFile($blackjackWay,'blackjackWay',$sName,'selectorData');
		$this->line(json_encode($sResult));


	}
	/**
	 * [_generateJsonFile 生成json数据文件]
	 * @param  [Array] $aData      [数据]
	 * @param  [String] $sFileName [文件名]
	 * @param  [String] $sName     [标题列名]
	 * @param  [String] $sDataName [json变量的名称]
	 * @return [Array]             [结果数组]
	 */
	private function _generateJsonFile($aData, $sFileName, $sName, $sDataName)
	{

		$sDataPath = Config::get('widget.data_path');
		$sPath     = realpath($sDataPath) . '/';
		$sSuffix   = '.blade.php';
		$sFile     = $sPath . $sFileName . $sSuffix;
		if (file_exists($sFile)){
			if (!is_writable($sFile)){
				return ['successful' => false, 'message' => 'File ' . $sFileName . ' not writable'];
			}
		}
		else{
			if (!is_writeable($sPath)){
				return ['successful' => false, 'message' => 'File ' . $sFileName . ' written in Path ' . $sPath . ' not writable'];
			}
		}
		//var_dump($sFile,"var " . $sDataName . "=" . json_encode($aData));exit;

		if(!empty($sDataName)) {
			$bSucc = @file_put_contents($sFile, "var " . $sDataName . "=" . json_encode($aData));
		}else{
			$bSucc = @file_put_contents($sFile,  json_encode($aData));
		}
		$sLangKey = ($bSucc ? ' generated' : ' write failed');
		$aReturn  = [
			'successful' => $bSucc,
			'message' => 'File ' . $sFile. $sLangKey
		];

		return $aReturn;
		// $sKey = $aReturn['successful'] ? 'success' : 'error';
		// return $this->goBackToIndex($sKey, $aReturn['message']);
	}



}
