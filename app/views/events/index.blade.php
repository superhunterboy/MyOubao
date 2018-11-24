@extends('l.admin')

@section('title')
@parent

@stop
@section('javascripts')
    @parent
    {{ script('bootstrap-3-switch') }}
@stop

@section('container')

    @ include('w.breadcrumb')
    @ include('w.notification')
    @ include('w._function_title')

<form method="GET" action="###" accept-charset="UTF-8" class="form-horizontal">
    sdf
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
