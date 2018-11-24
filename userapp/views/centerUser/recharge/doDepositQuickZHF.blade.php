<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <script type="text/javascript" src="/assets/third/zhf/jquery-1.8.0.js"></script>
        <script type="text/javascript" src="/assets/third/zhf/jquery.qrcode.js"></script>
        <script type="text/javascript" src="/assets/third/zhf/utf.js"></script>
    </head>
    <body>
        <div id="showqrcode">正在跳转...</div>
        <form name="dinpayForm" method="post" id="wxpay" action="{{ $sUrl }}">
            @foreach($aInputData as $key => $value)
            <input type="hidden" name="{{ $key }}" value="{{ $value }}" />
            @endforeach
            <input type="hidden" name="___DepositUrl" value="{{ $___DepositUrl }}" />
        </form>
        <button id="submit">提交支付参数</button>
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

$(document).ready(function () {

    $("#submit").click(function () {

        var formParam = $("#wxpay").serialize();                // 序列化表单内容为字符串  
        $.ajax({
            type: "post",
            url: "{{ $sUrl }}",
            data: formParam,
            dataType: "text",
            success: function (data, textStatus) {
                $("#xmldata").text(data);
                var result_code = $(data).find("result_code").text();
                if (result_code == "0") {
                    var qrcode = $(data).find("qrcode").text();
                    sQrcode(qrcode);
                } else {
                    $("#showqrcode").text("发生错误，原因请看resp_code或者result_desc值的描述");
                }
            },
            error: function () {
                alert("连接失败");
            }
        });
    });
    $("#submit").trigger('click');
});
        </script>
    </body>
</html>