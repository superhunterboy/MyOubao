@extends('l.base')

@section('title')
博猫周年盛典，千万金喜回馈！
@parent
@stop

@section ('styles')
@parent
    <link rel="stylesheet" type="text/css" href="/events/anniversary/images/anniversary.css" />
@stop

@section('container')
    @include('w.public-header')
    
    <div class="content">
        <div class="anniversary-container">
            <div class="anniversary"></div>
        </div>
        <div class="millinback-container">
            <div class="millinback"></div>
        </div>
        <div class="th-activities-container">
            <div class="activity-time">活动时间：2016年01月01日00:00:00-2016年1月31日23:59:59</div>
        </div>
        <div class="fi-activities-outer-container">
            <div class="btn-angle-new-user"></div>
            <div class="btn-angle-iphone"></div>
            <div class="btn-angle-total-bonus"></div>
            <div class="btn-angle-daily-two"></div>
            <div class="btn-angle-daily-one"></div>
        </div>
    </div>
    
<!--     <div class="notice">
        <div class="notice-content"></div>
        <div class="notice-cancel"></div>
    </div> -->


    <div class="mask">
        <div class="dice-bonus-one">
            <div class="bonus-one-cancel bonus-cancel"></div>
        </div>
        <div class="dice-bonus-two">
            <div class="bonus-two-cancel bonus-cancel"></div>
        </div>
        <div class="dice-bonus-total">
            <div class="bonus-total-cancel bonus-cancel"></div>
        </div>
        <div class="iphone-bonus">
            <div class="bonus-iphone-cancel bonus-cancel"></div>
        </div>
        <div class="new-user-bonus">
            <div class="bonus-new-user-cancel bonus-cancel"></div>
        </div>
<!--         <div class="bonus-wheel-container">
            <img class="bonus-wheel" src="/events/anniversary/images/bonus-wheel.png">
            <div class="bonus-wheel-pointer-cover"></div>
            <div class="bonus-wheel-pointer">
                <div class="bonus-wheel-end-time">
                    <font class="bonus-wheel-time-text">本期截止时间</font>
                    <font class="bonus-wheel-time-time">
                        <span class="left-hour">00</span>:<span class="left-min">00</span>:<span class="left-sec">00</span>
                    </font>
                </div>
                <div class="bonus-wheel-curr-avaliable">本期有效投注额
                    <br/>
                    <font class="curr-money bonus-wheel-curr-money">0元</font>
                </div>
            </div>
            <div class="bonus-wheel-cancel"></div>

            <div class="result-notice">
                <div class="result-notice-content"></div>
                <div class="result-notice-cancel">确定</div>
            </div>
        </div> -->
    </div>


    @include('w.footer')
@stop

@section('end')
<script>
(function($){


    $(".bonus-cancel").on("click", function() {
        $(".mask").hide();
        $(this).parent().hide();
    })
    $(".btn-angle-new-user").on("click", function() {
        $(".mask").show();
        $(".new-user-bonus").show();
    })
    $(".btn-angle-iphone").on("click", function() {
        $(".mask").show();
        $(".iphone-bonus").show();
    })
    $(".btn-angle-total-bonus").on("click", function() {
        $(".mask").show();
        $(".dice-bonus-total").show();
    })
    $(".btn-angle-daily-two").on("click", function() {
        $(".mask").show();
        $(".dice-bonus-two").show();
    })
    $(".btn-angle-daily-one").on("click", function() {
        $(".mask").show();
        $(".dice-bonus-one").show();
    })

    
})(jQuery);
</script>

<script type="text/javascript">

</script>
@parent
@stop


