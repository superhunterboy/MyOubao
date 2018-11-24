<?php

$iTimes    = pow(10,6);
$n = 10;
$m = 2;
$functions = ['check','check2'];
$aAvg = [];
$sCode     = '2 2+3 4 5';
unlink('a.txt');
foreach ($functions as $function){
    writeLog($function . ':');
    for ($i = 0,$aResults = []; $i < $n; $i++){
        $t1 = microtime(true);
        for ($j = 0; $j < $iTimes; $j++){
            $function($sCode);
        }
        $aResults[ $i ] = number_format(microtime(true) - $t1,6,'.','');
    }
    writeLog(var_export($aResults,true));
    $aAvg[] = $avg    = number_format(array_sum($aResults) / count($aResults),6,'.','');
    writeLog('AVG: ' . $avg . ' Seconds' . "\n");
}

//print_r($aAvg);
writeLog("Result");
$slowkey = (($diffTime = $aAvg[1] - $aAvg[0]) > 0) + 0;
$fastkey = 1 - $slowkey;
$difftime = $aAvg[$slowkey] - $aAvg[$fastkey];
$rate = number_format($difftime / $aAvg[$slowkey] * 100,2);
writeLog("{$functions[$fastkey]} is faster: $rate%");

function writeLog($txt){
    $file = 'a.txt';
    file_put_contents($file, $txt . "\n", FILE_APPEND);
}

function check($sWinningNumber){
    $pattern = '/[^\d]/';
    return preg_replace($pattern,'',$sWinningNumber);
}

function check2($sWinningNumber){
    $aNeedChars = [',','|','+',' '];
    return str_replace($aNeedChars,'',$sWinningNumber);
}
