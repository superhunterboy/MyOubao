@extends('l.admin', array('active' => $resource))

@section('title')
@parent
{{ $sPageTitle }}
@stop

@section('container')
@include('w.breadcrumb')
@include('w.notification')
@include('w._function_title')

@include('fund.exceptionDeposit.detailForm')

@stop

@section('end')
{{ script('bootstrap-switch') }}
@parent

<script>
    function modal(href)
    {
        $('#real-delete').attr('action', href);
        $('#myModal').modal();
    }

    $(function () {
        $('#offline_deposit').click(function () {
            $('#refund_step').val('offline');
            $('#refund_form').submit();
            
        });
        $('#mownecum_withdraw').click(function () {
            $('#refund_step').val("mownecum");
            $('#refund_form').submit();
        });
    });
</script>
@stop
