@if(Session::get('is_client'))
    @include('l.client.base')
@else
<!DOCTYPE HTML>
<html lang="en-US">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge"  />
        <meta name="description" content="@yield('description')" />
        <meta name="keywords" content="@yield('keywords')" />
        <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
        <title>
            @section('title')
            - 欧豹娱乐
            @show
        </title>
        @section ('styles')
            <style type="text/css">
                @font-face {
                font-family: 'Bomao';
                src: url('/assets/images/global/Bomao.eot');
                src: local('?'), url('/assets/images/global/Bomao.woff') format('/assets/images/global/woff'), url('/assets/images/global/Bomao.ttf') format('truetype'), url('/assets/images/global/Bomao.svg') format('svg');
                }
                @font-face{
                    font-family: dyAvenir;
                    src:url('/assets/images/global/dyAvenir.ttf');
                }

   /*             @font-face{
                    font-family:dyFzzhs;
                    src:url('/assets/images/global/dyFzzhs.ttf');
                }*/
                @font-face{
                    font-family:dyBebas;
                    src:url('/assets/images/global/dyBebas.ttf');
                }

                @font-face{
                    font-family:FZLanTingHei-R-GBK;
                    src:url('/assets/images/global/FZLanTingHei-R-GBK.ttf');
                }
            </style>
            {{ style('global-v4')}}

            {{ style('animate') }}
            {{ style('font-awesome')}}
        @show
    </head>

    @section('body')
    <body>
        @section('start')
        <input type="hidden" name="_token" id="J-global-token-value" value="" />
        @show

        @yield('container')

        

        


        @section('scripts')
            {{ script('jquery-1.9.1') }}
            {{ script('base-all') }}
            {{ script('jquery.easing') }}
            {{ script('jquery.lavalamp') }}


        @show

        @include('w.notification')

        @section('end')

        @show
<script type="text/javascript">
function openKF() {
	var url = '{{SysConfig::readValue("KFURL")}}';  //转向网页的地址;
    var name = '';                            //网页名称，可为空;
    var iWidth = 750;                          //弹出窗口的宽度;
    var iHeight = 500;                         //弹出窗口的高度;
    //获得窗口的垂直位置
    var iTop = (window.screen.availHeight - 30 - iHeight) / 2;
    //获得窗口的水平位置
    var iLeft = (window.screen.availWidth - 10 - iWidth) / 2;
    window.open(url, name, 'height=' + iHeight + ',,innerHeight=' + iHeight + ',width=' + iWidth + ',innerWidth=' + iWidth + ',top=' + iTop + ',left=' + iLeft + ',status=no,toolbar=no,menubar=no,location=no,resizable=no,scrollbars=0,titlebar=no');
}
(function($){
	 $(".J-a-refreshAmount").click(function () {
		 $(".c_check1").hide();
		 $.ajax({
   		 url: '/index.php?flag=availableBalance',
   		 type: 'get',
   		 dataType: 'json',
   		 success: function (data) {
       		 if (data['isSuccess']) {
		        		 var balance = data['data'];
		        		 $("#J-top-user-balance").html(balance);
		        		 $(".c_check1").show();
					}
   		     }
		 });

   });
})(jQuery);
</script>
    </body>
    @show
</html>
@endif