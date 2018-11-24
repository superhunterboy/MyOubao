<?php

# 移动端下载

class MobileDownloadController extends BaseController
{

    //移动端那安装包下载
    public function download()
    {
        $file =  Config::get('var.mobile_package_download_path')."Bomao.ipa";//需要下载的文件
        if (!file_exists($file)) {//判断文件是否存在
            $aData['error'] = '文件不存在';
            return $aData;
        }
        $fp = fopen($file, "r+");//下载文件必须先要将文件打开，写入内存
        $file_size = filesize($file);//判断文件大小
        //返回的文件
        Header("Content-type: application/octet-stream");
        //按照字节格式返回
        Header("Accept-Ranges: bytes");
        //返回文件大小
        Header("Accept-Length: " . $file_size);
        //弹出客户端对话框，对应的文件名
        Header("Content-Disposition: attachment; filename=" .basename($file));
        //防止<span id="3_nwp" style="width: auto; height: auto; float: none;"><a id="3_nwl" href="http://cpro.baidu.com/cpro/ui/uijs.php?app_id=0&c=news&cf=1001&ch=0&di=128&fv=17&is_app=0&jk=6a90f1dc977b4125&k=%B7%FE%CE%F1%C6%F7&k0=%B7%FE%CE%F1%C6%F7&kdi0=0&luki=2&n=10&p=baidu&q=06011078_cpr&rb=0&rs=1&seller_id=1&sid=25417b97dcf1906a&ssp2=1&stid=0&t=tpclicked3_hc&tu=u1922429&u=http%3A%2F%2Fwww%2Eadmin10000%2Ecom%2Fdocument%2F971%2Ehtml&urlid=0" target="_blank" mpid="3" style="text-decoration: none;"><span style="color:#0000ff;font-size:14px;width:auto;height:auto;float:none;">服务器</span></a></span>瞬时压力增大，分段读取
        $buffer = 1024;
        while (!feof($fp)) {
            $file_data = fread($fp, $buffer);
            echo $file_data;
        }
        //关闭文件
        fclose($fp);
    }

}
