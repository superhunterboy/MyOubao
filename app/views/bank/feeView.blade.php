@extends('l.admin', ['active' => $resource])
@section('title')
@parent
{{ $sPageTitle }}
@stop

@section('javascripts')
    @parent
    {{ script('bootstrap-3-switch') }}
@stop

@section('container')

    @include('w.breadcrumb')
    @include('w.notification')
    @include('w._function_title')

<form method="GET" action="###" accept-charset="UTF-8" class="form-horizontal">
    <table class="table table-bordered table-striped">
        <tbody>
             <tr>
                <th  class="text-right col-xs-2"> 银行名称：</th>
                <td>{{$oBank->name}}</td>
            </tr>

            <tr>
                <th  class="text-right col-xs-2"> 返还金额：</th>
                <td>
                @foreach($aFeeExpressions as $k => $fe)
                <?php
                    $operator_left = $operator_right = '';
                    $amount_left = $amount_right = '';
                    if(isset($fe['x']['>'])) {
                        $operator_left = '&lt;';
                        $amount_left = $fe['x']['>'];
                    } else if(isset($fe['x']['>='])) {
                        $operator_left = '&le;';
                        $amount_left = $fe['x']['>='];
                    }
                    if(isset($fe['x']['<'])) {
                        $operator_right = '&lt;';
                        $amount_right = $fe['x']['<'];
                    } else if(isset($fe['x']['<='])) {
                        $operator_right = '&le;';
                        $amount_right = $fe['x']['<='];
                    } else {
                        $operator_right = '&lt;';
                        $amount_right = '+&#8734;';
                    }
                ?>
                <div class="j-add-fee clearfix" style="margin-bottom:2px;">
                    <!-- 条件区 START -->
                    <label class="control-label" style=" width:80px; float:left; font-weight: bold;">
                        {{$amount_left}}
                    </label>
                    <label for=" " class="control-label " style="text-align: center; width:120px; float:left;">
                        <font color="red"> {{$operator_left}} </font>用户充值金额 <font color="red"> {{$operator_right}} </font>
                    </label>
                    <label class="control-label" style=" width:80px; float:left; font-weight: bold; text-align: left;">
                        {{$amount_right}}
                    </label>
                    <!-- 条件区 END -->
                    <label for=" " class="control-label " style="text-align: center; width:120px; float:left;">，所返手续费为：</label>
                    <!-- 取值区 START -->
                   <div  style=" width:60px; float:left;">
                    </div>
                    <div  style=" width:120px; float:left;">
                        <span>
                            @if(isset($fe['y']['='])) 
                            {{$fe['y']['=']}}
                             <label for=" " class="control-label ">元</label>
                            @elseif(isset($fe['y']['%']))
                            {{$fe['y']['%']}}
                            <label for=" " class="control-label "><font color="red"> %</font></label>
                            @endif
                        </span>
                    </div>
                    <!-- 取值区 END -->
                </div>
                @endforeach
            </td>
        </tr>

        <tr>
                <th  class="text-right col-xs-2">&nbsp;</th>
                <td>
                    <a href="{{ route($resource . '.index') }}" id="cancle" class="btn btn-embossed btn-danger">返回</a>
           </td>
           </tr>

      </tbody>
    </table>
</form>





@stop

@section('end')
@parent
<script type="text/javascript">
   $(function(){

    var dHtml= '<span class="btn btn-embossed  btn-danger j-delete" onclick="removeDiv(this);">删除</span>';
    var domHtml = $('.j-add-fee:first').html();
    var allHtml = '<div class="j-add-fee clearfix" style="margin-bottom:2px;">'+ domHtml + dHtml+  '</div>';

    $('#add-fee-btn').click(function(){
        $('.j-fee-box').append(allHtml);
    });

   });

     function removeDiv(dome){
        $(dome).parent('div.j-add-fee').remove();
    }

</script>

@stop
