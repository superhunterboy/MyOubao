<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                <script type="text/javascript" src="/assets/third/zhf/jquery-1.8.0.js"></script>
        <script type="text/javascript" src="/assets/third/zhf/jquery.qrcode.js"></script>
        <script type="text/javascript" src="/assets/third/zhf/utf.js"></script>
    </head>
    <body><div id="showqrcode">正在跳转...</div>
    <?php
    $res = send_post($sUrl, $aInputData);
    Log::info($res);
    Log::info('----------------------zesheng------------------------------');
    $aRes = json_decode($res, true);
    $sUrl = '';
    if (array_get($aRes, 'code') == '00') {
        $aData = array_get($aRes, 'data');
        $sUrl = array_get($aData, 'url');
//        echo $sUrl;
    }
//    echo $res;

    function send_post($url, $post_data) {
        $postdata = http_build_query($post_data);
        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-type:application/x-www-form-urlencoded',
                'content' => $postdata,
                'timeout' => 15 * 60
            ) // 超时时间（单位:s）
        );
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        return $result;
    }

    ?>
    <script>

        function sQrcode(qdata) {
            $("#showqrcode").empty().qrcode({// 调用qQcode生成二维码
                render: "canvas", // 设置渲染方式，有table和canvas，使用canvas方式渲染性能相对来说比较好
                text: qdata, // 扫描了二维码后的内容显示,在这里也可以直接填一个网址或支付链接
                width: "200", // 二维码的宽度
                height: "200", // 二维码的高度
                background: "#ffffff", // 二维码的后景色
                foreground: "#000000", // 二维码的前景色
                src: ""                                                 // 二维码中间的图片
            });

        }
sQrcode('{{$sUrl}}');
    </script>
    </body>
</html>