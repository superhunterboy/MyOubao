<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;


/**
 * 抓取天气图片
 */
class GetWeatherIcons extends BaseCommand {
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'jc:football-weathericons';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    protected $sFileName = 'football-weathericons';
    
    protected function getArguments() {
      return array(
          //array('lottery_id', InputArgument::REQUIRED, null),
//          array('page', InputArgument::OPTIONAL, null),
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
        $this->iconPath = dirname(app_path()) . '/userpublic/assets/images/sports/weather_icons';
        $this->pageSize = 1000;
        
        $iOffset = 0;
        $iPageSize = $this->pageSize;
        
        while(true){
            $aMatches = \JcModel\JcMatchOriginal
                    ::orderby('id','desc')
                    ->limit($this->pageSize)
                    ->offset($iOffset)
                    ->get(['id', 'weather_pic']);
            
//            $this->info("offset: {$iOffset}");
            $iOffset += $iPageSize;
            if (count($aMatches) <= 0){
                break;
            }

            $count = 0;
            foreach($aMatches as $oMatch){
                $sWeatherPic = $oMatch->weather_pic;
                
                if (!preg_match('/\/([\w]+)\.([\w]+)$/isU', $sWeatherPic, $matches)){
                    continue;
                }
                $sWeatherKey = $matches[1].'.'.$matches[2];

                $sWeatherPicPath = "{$this->iconPath}/{$sWeatherKey}";
                if (!file_exists($sWeatherPicPath)){
                    if ($this->_saveFile($sWeatherPic, $sWeatherPicPath)){
                        $count++;
                        $this->info('save success. path:'. $sWeatherPicPath);
                    }
                }
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

    private function _saveFile($url, $path){
        $sIconText = $this->_curl($url);
        if ($sIconText){
            $fp2=fopen($path,'w');
            $size = fwrite($fp2,$sIconText);
            fclose($fp2);
            return $size > 0;
        }
    }
}