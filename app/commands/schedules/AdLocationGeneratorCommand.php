<?php
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
 * TODO 未完成，批量生成广告模板
 */
class AdLocationGeneratorCommand extends Command {
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'admin-tool:generate-ad-locations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Batch generate ad location files.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    public function fire()
    {
        $result = [];
        $oAdLocations = AdLocation::where('is_closed', '=', 0)->get();
        foreach($oAdLocations as $key => $oAdLocation) {
            $iLocationId = $oAdLocation->id;
            $sTypeName = $oAdLocation->type_name;
            if (! $sTypeName) continue;
            $contents = $this->getTemplateContent($sTypeName);
            $pz = '/<!\-\-\{split\}\-\->(.*)<!\-\-\{endsplit\}\-\->/Us';
            preg_match_all($pz, $contents, $mat);
            $sTempHtml = $mat[1];

            $aAdInfos = AdInfo::getAdInfosByLocationId($iLocationId); //当前广告位广告详情数据
            //为生成文件做数据准备
            $rTemp = [];
            foreach ($aAdInfos as $key2 => $oAdInfo) {
                $sHtml = [];
                $search = [];
                $replace = [];
                $oAdInfo->num = $key2 + 1;
                $oAdInfo->pic_width = $oLocation->pic_width;
                $oAdInfo->pic_height = $oLocation->pic_height;
                foreach ($oAdInfo->toArray() as $key3 => $value) {
                    $search[] = '%' . $key3 . '%';
                    $replace[] = $value;
                }
               $rTemp[]= str_replace($search, $replace, $sTempHtml);
            }
            //循环填充模板
            $aLiImg = [];
            $aLiNum = [];
            foreach ($rTemp as $key => $value) {
                $aLiImg[] = $value[0];
                if(count($value) >1){
                    $aLiNum[] =  $value[1];
                }

            }
            $aLiImg = implode(" ",  $aLiImg);
            $aLiNum = implode(" ",  $aLiNum);

            //合并分离的模板，得到完成新文件内容
            $newHtml = $this->newTemplates($sTypeName,  $aLiImg , $aLiNum);
            //存储模板文件，并返回状态
            $aReturn = $this->generateHtmlFile($newHtml, $iLocationId);
            $result[] = $aReturn;
        }
        var_dump($result);
    }

    private function getTemplateContent($adTypeName) {
        $filename = Config::get('var.template') . strtolower($adTypeName) . ".html";
        $handle   = fopen($filename, "r");
        $contents =fread($handle, filesize ($filename));
        fclose($handle);
        return $contents;
    }
}