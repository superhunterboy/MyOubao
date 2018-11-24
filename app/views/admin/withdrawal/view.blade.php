@extends('l.admin', ['active' => $resource])
@section('title')
@parent
{{ $sPageTitle }}
@stop

@section('container')
    @include('w.breadcrumb')
    @include('w.notification')
    <a class="btn btn-default" style="float:right;" href="javascript:history.go(-1);">{{ __('_basic.return') }}</a>
    @include('w._function_title', ['id' => $data->id , 'parent_id' => $data->parent_id])

    <table class="table table-bordered table-striped" id="J-tabel">
        <tbody>

    @if(!empty($sParentTitle))

    {{-- <tr>
        <th  class="text-right col-xs-2">{{ __('_basic.parent',null,2) }}</th>
        <td>{{ $sParentTitle }}</td>
    </tr> --}}
    @endif
    <?php
    $i = 0;
    foreach ($aColumnSettings as $sColumn => $aSetting) {
        if (isset($aViewColumnMaps[ $sColumn ])) {
            $sDisplayValue = $data->{$aViewColumnMaps[ $sColumn ]};
        } else {
            if (isset($aSetting[ 'type' ])) {
                switch ($aSetting[ 'type' ]) {
                    case 'ignore':
                        continue 2;
                        break;
                    case 'bool':
                        $sDisplayValue = $data->$sColumn ? __('Yes') : __('No');
                        break;
                    case 'text':
                        $sDisplayValue = nl2br($data->$sColumn);
                        break;
                    case 'select':
                        $sDisplayValue = __($sLangPrev . Str::slug(strtolower(!is_null($data->$sColumn) ? ${$aSetting[ 'options' ]}[ $data->$sColumn ] : null)));
                        break;
                    case 'numeric':
                    case 'date':
                    default:
                        $sDisplayValue = $data->$sColumn;
                }
            } else {
                $sDisplayValue = $data->$sColumn;
            }
        }
    ?>

    <tr>
        <th class="text-center" style="width:30px;">{{ $i+1 }}</th>
        <th class="text-right col-xs-2">{{ __($sLangPrev . $sColumn, null, 2) }}</th>
        <td class="data-copy">{{ $sDisplayValue }}</td>
        <td num="{{ $i+1 }}"></td>
    </tr>
    <?php
        $i++;
    }
    ?>
    </tbody>
    </table>
    @if (isset($aActions) && $aActions && in_array($iStatus, [0,1,12]))
    <form id="processForm" name="processForm" method="get" action="">
        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
        <input type="hidden" name="_method" value="PUT" />

        提款通道:
        @if (SysConfig::readValue('sdpay_enable'))
            <input type="radio" name="is_sdpay" value="1" @if($data->isSDPay()) checked="checked" @endif >Sdpay</input>
        @endif

        @if (SysConfig::readValue('dashpay_enable'))
            <input type="radio" name="is_sdpay" value="0" @if(!$data->isSDPay()) checked="checked" @endif>DashPay</input>
        @endif


        <input type="radio" name="is_sdpay" value="2">通汇卡</input>
        <input type="radio" name="is_sdpay" value="3">优付</input>


        <br/>

        可选操作:
        @if ($iStatus == 0 || $iStatus == 12 || $iStatus == 1)
        <input type="radio" name="processStatus" value="1" ac="{{ $aActions['verified'] }}" >审核通过</input>
        <input type="radio" name="processStatus" value="2" ac="{{ $aActions['refused'] }}" >审核不通过</input>
        @endif
        @if ($iStatus == 12 || $iStatus == 0)
        <input type="radio" name="processStatus" value="3" ac="{{ $aActions['waiting'] }}" >审核待定(存在风险)</input>
        @endif
        <br/>

        <div class="refusedRemark" style="display:none">
        拒绝理由(50个字符以内):<textarea name="error_msg"></textarea>
        </div>
        <div class="waitingRemark" style="display:none">
        备注(50个字符以内):<textarea name="remark"></textarea>
        </div>

        <br/>


        <button type="submit" class="btn btn-success">确认执行</button>
    </form>
    @endif
@stop




@section('end')
{{ script('ZeroClipboard')}}
    @parent
    <script>
    $('input[name=processStatus]').change(function () {
        debugger;
        var ac = $(this).attr('ac');
        var val = $(this).val();
        if (val == 2) {
            $('.refusedRemark').show();
            $('.waitingRemark').hide();
        }
        if (val == 3) {
            $('.refusedRemark').hide();
            $('.waitingRemark').show();
        }
        $('#processForm').attr('action', ac);
    });
    $(function(){
        //初始化加入按钮
        var table = $('#J-tabel'), btn = '<input type="button" class="btn btn-xs btn-embossed btn-default" value="点击复制" />';
        for(var i = 0 ; i< table.find('tr').length ; i++){
            if(i== 6 || i== 8 ||i== 9 ||i== 10){
                table.find('tr').eq(i-1).find('td:eq(1)').html(btn).end().find("input").attr("id" , "J-button-"+i )
            }
        };
        //载入复制
        ZeroClipboard.setMoviePath('/assets/js/ZeroClipboard.swf');

        var clip_name = new ZeroClipboard.Client(),
            clip_card = new ZeroClipboard.Client(),
            clip_money = new ZeroClipboard.Client(),
            clip_msg = new ZeroClipboard.Client(),
            table = $('#J-table'),
            fn = function(client){
                var el = $(client.domElement),
                    value = $.trim(el.parent().parent().find('.data-copy').text());
                client.setText(value);
                alert('复制成功:\n\n' + value);
            };

          clip_name.setCSSEffects( true );
          clip_card.setCSSEffects( true );
          clip_money.setCSSEffects( true );
          clip_msg.setCSSEffects( true );

          clip_name.addEventListener( "mouseUp", fn);
          clip_card.addEventListener( "mouseUp", fn);
          clip_money.addEventListener( "mouseUp", fn);
          clip_msg.addEventListener( "mouseUp", fn);

          clip_name.glue('J-button-6');
          clip_card.glue('J-button-8');
          clip_money.glue('J-button-9');
          clip_msg.glue('J-button-10');





    })



    //----------------------//
        function modal(href)
        {
            $('#real-delete').attr('action', href);
            $('#myModal').modal();
        }
    </script>
@stop

