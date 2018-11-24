<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;


/**
 * 抓取队伍图片
 */
class GetTeamIcons extends BaseCommand {
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'jc:football-teamicons';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    protected $sFileName = 'football-teamicons';
    
    protected function getArguments() {
      return array(
          //array('lottery_id', InputArgument::REQUIRED, null),
          array('page', InputArgument::OPTIONAL, null),
      );
    }
    
    protected function getOptions()
    {
            return array(
//                array('force', null, InputOption::VALUE_OPTIONAL, 'skip check update time.', null),
            );
    }
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
            parent::__construct();
            
            $this->logFile = $this->logPath . DIRECTORY_SEPARATOR . $this->sFileName;
    }
    
    public function fire()
    {
        $this->iconPath = dirname(app_path()) . '/userpublic/assets/images/sports/icons';
        $this->pageSize = 1000;
        
        $iOffset = 0;
        $iPageSize = $this->pageSize;
        
        while(true){
            $aMatches = \JcModel\JcMatchOriginal
                    ::orderby('id','desc')
                    ->limit($this->pageSize)
                    ->offset($iOffset)
                    ->get(['id', 'h_id', 'a_id']);
            
//            $this->info("offset: {$iOffset}");
            $iOffset += $iPageSize;
            if (count($aMatches) <= 0){
                break;
            }

            $count = 0;
            foreach($aMatches as $oMatch){
                $mid = $oMatch->id;
                $hid = $oMatch->h_id;
                $aid = $oMatch->a_id;

                $sHomeIconPath = "{$this->iconPath}/{$hid}.png";
                $sAwayIconPath = "{$this->iconPath}/{$aid}.png";
                if (file_exists($sHomeIconPath) && file_exists($sAwayIconPath)){
                    continue;
                }

                $url = "http://info.sporttery.cn/football/info/fb_match_asia.php?m={$mid}";

                $sContent = $this->_curl($url);
                if (!$sContent){
                    break;
                }
                $aIconList = $this->getIconUrlList($sContent);
                if (!file_exists($sHomeIconPath) && $aIconList['home_team']){
                    if ($this->_saveIcon($aIconList['home_team'], $sHomeIconPath)){
                        $count++;
                        $this->info('save success. path:'. $sHomeIconPath);
                    }
                }
                if (!file_exists($sAwayIconPath) && $aIconList['away_team']){
                    if ($this->_saveIcon($aIconList['away_team'], $sAwayIconPath)){
                        $count++;
                        $this->info('save success. path:'. $sAwayIconPath);
                    }
                }
    //            foreach($aIconList as $sIconUrl){
    //                $sPath = 
    //                $bSucc = $this->_curl($sIconUrl, $sPath);
    //            }
            }
        }
        
        $this->info("count: {$count}");
        $this->info('success: done');
    }

    private function _curl($url){
        //循环避免网络不稳定
        for ($i=0;$i<20;$i++){
            $sCurlRes = $this->_send($url);
            if ($sCurlRes){
                return $sCurlRes;
            }
        }
        $this->writeLog('failed . url: '. $url);
        return false;
    }
    
    public function getIconUrlList($content = ''){
        $utf8Content = mb_convert_encoding($content, 'UTF-8', 'gb2312');

        $arr = [
            'home_team' => null,
            'away_team' => null,
        ];
        if (preg_match('/<div class="HomeBoxLogo"><img src="(.*)"/isU', $utf8Content, $matches)){
            $arr['home_team'] = $matches[1];
        }
        if (preg_match('/<div class="GuestBoxLogo"><img src="(.*)"/isU', $utf8Content, $matches)){
            $arr['away_team'] = $matches[1];
        }
        
        return $arr;
    }
    
    private function _send($url = ''){
        if (!$url){
            return false;
        }
        $this->info("loading url: {$url}");
        $this->writeLog("loading url: {$url}");
        
        $header = [];
        $oCurl = new MyCurl($url);
        $oCurl->setReferer($url);
//        $oCurl->setTimeout(3);
        $oCurl->createCurl($url, $header);
        $oCurl->execute();
        $sCurlRes  = $oCurl->__tostring();
        $sCurlStatus = $oCurl->getHttpStatus();
        if ($sCurlStatus != 200){
            $this->error('curl status: ' . $sCurlStatus);
            return false;
        }
        if (empty($sCurlRes)){
            $this->error('curl result is empty');
            return false;
        }
        return $sCurlRes;
    }

    private function _saveIcon($url, $path){
        $sIconText = $this->_curl($url);
        if ($sIconText){
            $fp2=fopen($path,'w');
            $size = fwrite($fp2,$sIconText);
            fclose($fp2);
            return $size > 0;
        }
    }
}