<!DOCTYPE html>
<html>
    <head>
        <title>
            @section('title')
            @show{{-- 页面标题 --}}
        </title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!--[if lt IE 9]>
            <script src="http://cdn.bootcss.com/html5shiv/3.7.0/html5shiv.min.js"></script>
            <script src="http://cdn.bootcss.com/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <script>
            (function(H){H.className=H.className.replace(/\bno-js\b/,'js')})(document.documentElement)
        </script>
        @section ('styles')
          {{ style('bootstrap-3.0.3') }}
          {{ style('main' ) }}
          {{ style('ui' ) }}
        @show
    </head>
    <body>
        <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <div class="navbar-header">
                <a class="navbar-brand" target="main" href="{{ route('admin.dashboard') }}">
                  {{ $title or '' }}</a>
            </div>
<div class="g-c">
    <?php $sTarget =  'main'; ?>
        <div class="heard-top">
          <div class="pull-left">
            <span class="menu-btn" id="J-side-tog-btn"></span>
            <a target="{{ $sTarget }}" href="{{ route('withdrawals.unverified') }}" withdrawals-box class="btn btn-link">提现审核 <span id="j-badge" class="badge badge-warning" style="display: none;"></span></a>
            <a  target="{{ $sTarget }}" href="{{ route('withdrawals.verified') }}" class="btn btn-link">提现出款 <span id="j-badge-f" class="badge badge-warning" style="display: none;"></span></a>
            <!--<a href="##" class="btn btn-link">异常 <span class="badge badge-danger"  style="display: none;"><i class="glyphicon glyphicon-bullhorn "></i></span></a>-->
          </div>

          <!-- <div class="btn-group">
            <button type="button" class="btn btn-b btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
              快捷菜单 <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" role="menu">
              <li><a href="#">快捷1 <span class="badge badge-danger">3</span></a></li>
              <li><a href="#">快捷2</a></li>
              <li><a href="#">快捷3</a></li>
              <li class="divider"></li>
              <li><a href="#">快捷4</a></li>
            </ul>
          </div>
          <div class="btn-group" role="group" aria-label="...">
              <button type="button" class="btn btn-b btn-sm btn-default">1</button>
            <button type="button" class="btn btn-b btn-sm btn-default">2</button>

            <div class="btn-group" role="group">
              <button type="button" class="btn btn-b btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                成组菜单
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu" role="menu">
                <li><a href="#">下拉</a></li>
                <li><a href="#">下拉</a></li>
              </ul>
            </div>
          </div> -->

        </div>
      </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">

              <!-- <ul class="nav navbar-nav">  TODO 调整一级菜单
                <li>
                    <a href="##">{{-- Str::title(__('_function.' . $menu['title'])) --}}</a>
                </li>
              </ul> -->

                <ul class="nav navbar-nav navbar-right">
                  <!-- <li>
                    <a href="#">
                        <span class="glyphicon glyphicon-retweet"></span>
                    </a>
                  </li> -->
                  <li>
                    <a  target="_parent" href="{{ route('admin-logout') }}">
                        <span class="glyphicon glyphicon-off" title="{{__('Logout')}}"></span>
                    </a>
                  </li>
                  <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        {{ Session::get('admin_username') }} <!-- <b class="caret"></b> --></a>
                    <!-- <span class="dropdown-arrow dropdown-arrow-inverse"></span>
                    <ul class="dropdown-menu">
                      <li><a href="#">修改信息</a></li>
                      <li><a href="#">安全管理</a></li>
                      <li class="divider"></li>
                      <li><a href="#">帮助中心</a></li>
                      <li><a href="#">建议反馈</a></li>
                    </ul> -->
                  </li>
                </ul>
            </div>
<div class="video"></div>
        </nav>

        @section('javascripts')
          {{ script('jquery-1.10.2') }}
          {{ script('bootstrap-3.0.3') }}
        @show
         <script type="text/javascript">
      $(function(){
        var role,
            startTime = 0,
            role = 'customer',
            url = '/alarm/withdraw',
            mp3 = '/assets/mp3/withdrawals.mp3',
            withdrawUrl = "{{ route('withdrawals.unverified','is_tester=0') }}";
          withdrawUrlF = "{{ route('withdrawals.verified','is_tester=0') }}";
          roleF = 'finance';
          urlF = '/alarm/withdraw-finance';
          mp3F = '/assets/mp3/withdrawals-finance.mp3';
        $('#j-badge').parent().attr('href', withdrawUrl);
        $('#j-badge-f').parent().attr('href', withdrawUrlF);
        if( role == 'customer' && {{ intval(Session::get('bFlagForCustomer') )}}){
          setInterval(function (){
            $.ajax({
               url: url,//提款
             })
             .done(function(data) {
                var count = parseInt(data);
                if( count < 1 ){
                  startTime = 0;
                  $('.video').html();
                  $('#j-badge').hide();
                }else{
                  $('#j-badge').show().html(count);
                  var newTime = new Date().getTime();
                  if(newTime-startTime > 30*1000){
                    if(/msie/.test(navigator.userAgent.toLowerCase())) {
                        $('.video').html('<bgsound  src="'+mp3+'" loop="1"/>');
                    } else {
                        $('.video').html('<audio  src="'+mp3+'" autoplay="autoplay"/>');
                    }
                    startTime = new Date().getTime();
                  }
                }
             });
          },5000);
        }
        if( roleF == 'finance' && {{ intval(Session::get('bFlagForFinance') )}}){
          setInterval(function (){
            $.ajax({
               url: urlF,//提款
             })
             .done(function(data) {
                var count = parseInt(data);
                if( count < 1 ){
                  startTime = 0;
                  $('.video').html();
                  $('#j-badge-f').hide();
                }else{
                  $('#j-badge-f').show().html(count);
                  var newTime = new Date().getTime();
                  if(newTime-startTime > 30*1000){
                    if(/msie/.test(navigator.userAgent.toLowerCase())) {
                        $('.video').html('<bgsound  src="'+mp3+'" loop="1"/>');
                    } else {
                        $('.video').html('<audio  src="'+mp3+'" autoplay="autoplay"/>');
                    }
                    startTime = new Date().getTime();
                  }
                }
             });
          },5000);
        }
      });

    </script>
    </body>
</html>