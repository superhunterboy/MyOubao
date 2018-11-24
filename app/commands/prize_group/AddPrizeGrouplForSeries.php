<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
 *   
 */
class AddPrizeGrouplForSeries extends BaseCommand {

    protected $sFileName = 'addPrizeGroup';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'prize_group:add';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'add prize group for new series.';

    protected function getArguments() {
        return array(
            array('series_id', InputArgument::REQUIRED, null),
        );
    }

    public function doCommand(& $sMsg = null) {
        $aProjects = ManProject::getUnSentCommissionsProjectIds();
        $aIds = [];
        foreach($aProjects as $oProject){
            $aIds[] = $oProject->id;
        }
        return $this->pushJob('SendCommission', ['projects' => $aIds], Config::get('schedule.send_commission'));
        $iSeriesId = $this->argument('series_id');
        $oSeries = Series::find($iSeriesId);
        if (!is_object($oSeries)) {
            $this->exitPro("missing series, series_id=" . $iSeriesId, false);
        }
        if ($iSeriesId == 2) {
            $iMaxPrizeGroup = 1980;
            $iType = 2;
        } else {
            $iMaxPrizeGroup = 2000;
            $iType = 1;
        }
        for ($i = 1963; $i <= 2000; $i++) {
            $oPrizeGroup = PrizeGroup::getObjectByParams(['series_id' => $iSeriesId, 'name' => $i]);
            if (is_object($oPrizeGroup)) {
                continue;
            }
            $oNewPrizeGroup = new PrizeGroup;
            $oNewPrizeGroup->series_id = $iSeriesId;
            $oNewPrizeGroup->type = $iType;
            $oNewPrizeGroup->name = $i;
            $oNewPrizeGroup->classic_prize = $i;
            $oNewPrizeGroup->water = number_format(($iMaxPrizeGroup - $i) / $iMaxPrizeGroup, 4);
            pr($i . '-' . $oNewPrizeGroup->water);
            $oNewPrizeGroup->save();
        }
    }

}
