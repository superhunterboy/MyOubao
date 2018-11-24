<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class updateCommissionRate extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'admin-tool:update-error-commission';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'update-error-commission';


	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$method = $this->argument('method');
		if (is_null($method)){
			$pageNum = 100;
			$projectIds = Config::get('m');
			if (!empty($projectIds)) {
				$projectCount = count($projectIds);
				$pages = intval($projectCount / $pageNum + 1);
				//分页处理数据
				for ($i = 0; $i < $pages; $i++) {
					//if($i==0) continue;

					$pageIds = array_slice($projectIds, $i * $pageNum, $pageNum);

					//取commissions
					$oProjectUser = Commission::whereIn('project_id', $pageIds)->where('status', 0)->get(['project_id', 'id', 'user_id', 'amount', 'lottery_id']);
					if ($oProjectUser->count() == 0) {
						$this->line('project_ids:' . implode(',', $pageIds) . '无数据');
						continue;
					}
					foreach ($oProjectUser as $opUser) {
						$pData = Project::where('id', $opUser->project_id)->where('user_id', $opUser->user_id)->get(['commission', 'amount', 'user_forefather_ids']);
						if ($pData->count() != 0) {//自己投注

							$pData = $pData->first();
							$rate = $pData->amount * $pData->commission;

						} else {
							$pData = Project::where('id', $opUser->project_id)->get(['id', 'commission', 'amount', 'user_forefather_ids', 'user_id', 'lottery_id'])->first();
							$fatherIds = $pData->user_forefather_ids;
							$fatherIds = explode(',', $fatherIds);
							$key = array_search($opUser->user_id, $fatherIds);
							if (isset($fatherIds[$key + 1])) {
								$nextUserId = $fatherIds[$key + 1];
							} else {
								$nextUserId = $pData->user_id;
							}


							if ($pData->lottery_id < 25)
								$seriesId = 1;
							else if ($pData->lottery_id >= 25 && $pData->lottery_id <= 33)
								$seriesId = 2;
							else if ($pData->lottery_id >= 34 && $pData->lottery_id <= 43)
								$seriesId = 3;
							else
								$seriesId = 4;
							$oNextUserCommissionSet = UserCommissionSet::where('user_id', $nextUserId)->where('series_set_id', $seriesId)->get(['commission_rate'])->first();
							$oUserCommissionSet = UserCommissionSet::where('user_id', $opUser->user_id)->where('series_set_id', $seriesId)->get(['commission_rate'])->first();
							if (!$oNextUserCommissionSet) continue;
	//						pr($oUserCommissionSet->commission_rate);
	//						pr($oNextUserCommissionSet->commission_rate);
	//						pr($pData->amount);
							$rate = ($oUserCommissionSet->commission_rate - $oNextUserCommissionSet->commission_rate) / 100 * $pData->amount;
						}
						$r = Commission::where('project_id', $opUser->project_id)->where('user_id', $opUser->user_id)->update(array('amount' => $rate));
	//					$queries = DB::getQueryLog();
	//					 $last_query = end($queries);
	//					 pr($last_query);
						$this->line('project_id:' . $opUser->project_id . ',user_id:' . $opUser->user_id . '更新完 ' . $rate . ', ' . $r);
						$this->line('------------------------------------------------');
					}
				}
			}
		}else{
			$this->insertNewCommission();
		}
	}

	public function insertNewCommission(){
		$pageNum = 100;
		$projectIds = Config::get('m');
		if (!empty($projectIds)) {
			$projectCount = count($projectIds);
			$pages = intval($projectCount / $pageNum + 1);
			for ($i = 0; $i < $pages; $i++) {
				$pageIds = array_slice($projectIds, $i * $pageNum, $pageNum);

				$oProjects = Project::whereIn('id',$pageIds)->get();
				if($oProjects->count() == 0)
				{
					$this->line('project_ids:' . implode(',', $pageIds) . '无数据');
					continue;
				}else{
					foreach($oProjects as $project)
					{
						$commissionCount = Commission::where('project_id',$project->id)->where('user_id',$project->user_id)->get()->count();
						if($commissionCount > 0)
						{
							$this->line("project_id:".$project->id.'重复。');
							continue;
						}
						$oSeriesWay = SeriesWay::find($project->way_id);
						$oLottery = Lottery::find($project->lottery_id);
						$oUser = User::find($project->user_id);
						$aExtraData = [
							'is_tester' => $oUser->is_tester,
						];
						$aProjectDetails = Project::compileProjectData($project, $oSeriesWay, $oLottery, $aExtraData);
						$oProject = new Project($aProjectDetails);
						$oProject->id = $project->id;
						$oProject->serial_number = $project->serial_number;
						$oProject->setUser($oUser);
						$oProject->setLottery($oLottery);
						$r =$oProject->saveCommissions();
						$this->line("project_id:".$project->id.'完成。'.$r);
					}
				}
			}
		}
	}
	protected function getArguments() {
		return array(
			array('method', InputArgument::OPTIONAL,null),
		);
	}



}
