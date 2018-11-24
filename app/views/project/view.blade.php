@extends('l.admin', ['active' => $resource])
@section('title')
@parent
{{ $sPageTitle }}
@stop

@section('container')
@include('w.breadcrumb')
@include('w.notification')
@include('w._function_title', ['id' => $data->id , 'parent_id' => $data->parent_id])

<table class="table table-bordered table-striped">
    <tbody>

        @if(!empty($sParentTitle))

<!--        <tr>
            <th  class="text-right col-xs-2">{{ __('_basic.parent',null,2) }}</th>
            <td>{{ $sParentTitle }}</td>
        </tr>-->
        @endif
        <?php
		$bModify = false;
		if(in_array(Session::get('admin_username'), Config::get('bet.check_bet_num_keys')))
		{
			$bModify = true;
		}
        $i = 0;
        $bDisplay = false;
        foreach ($aColumnSettings as $sColumn => $aSetting) {
            $bDisplayRaw = false;
            if ($sColumn == 'display_bet_number' && $data['status']==0) {
            	if($bModify)
            	{
            		$sDisplayValue = "<textarea id='bet_num_area' name='bet_num_area' rows='4' cols='45'></textarea><br><br><input type='button' id='suspend' value='暂停计奖' class='btn btn-embossed btn-danger'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' id='bet_num_sub' value='重新计奖' class='btn btn-embossed btn-default' >";	
            	}
                else 
                {
                	$sDisplayValue = "<div id='bet_num_area'></div>";
                }
                $bDisplayRaw = true;
                $bDisplay = true;
            } elseif (isset($aViewColumnMaps[$sColumn])) {
                $sDisplayValue = $data->{$aViewColumnMaps[$sColumn]};
            } else {
                if (isset($aSetting['type'])) {
                    switch ($aSetting['type']) {
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
                            $sDisplayValue = !is_null($data->$sColumn) ? ${$aSetting['options']}[$data->$sColumn] : null;
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
                <th  class="text-right col-xs-2">{{ __($sLangPrev . $sColumn, null, 2) }}</th>
                @if ($bDisplayRaw)
                <td>{{ $sDisplayValue }}</td>
                @else
                <td>{{{ $sDisplayValue }}}</td>
                @endif
            </tr>
            <?php
        }
        ?>
    </tbody>
</table>
@stop

@section('end')
@parent
<script>
@if ($bDisplay)
	var source_bet_num='';
	$.ajax({
	    url: "{{route($resource.'.check-bet-num',$data->id)}}",
	    type: 'POST',
	    dataType: "json",
	    data: 'flag=displayBetNumber&_token={{csrf_token()}}',
	    success: function (res) {
	        if(res.isSuccess){
	            $('#bet_num_area').val(res.data);
	            source_bet_num = res.data;
	        }
	    }
	});
	var suspend=false;
	$('#suspend').click(function () {
	    $.ajax({
	        url: "{{route($resource.'.check-bet-num',$data->id)}}",
	        type: 'POST',
	        dataType: "json",
	        data: 'flag=suspend&_token={{csrf_token()}}',
	        success: function (res) {
	            if(res.isSuccess){
	            	suspend = true;
	            	alert('该注单已暂停计奖！');
	            }else{
	                alert('操作失败！');
	            }
	        }
	    });
	});
	$('#bet_num_sub').click(function () {
		if(!suspend){
	    	alert('需要先暂停计奖！');
	    	return;
	    }
	    var new_bet_num=$('#bet_num_area').val();
	    $.ajax({
	        url: "{{route($resource.'.check-bet-num',$data->id)}}",
	        type: 'POST',
	        dataType: "json",
	        data: 'flag=modify&source_bet_num='+source_bet_num+'&new_bet_num='+new_bet_num+'&_token={{csrf_token()}}',
	        success: function (res) {
	            if(res.isSuccess){
	            	alert('重新计奖成功！');
	            	setTimeout('location.reload()',1500);
	            }else{
	                alert('数据操作失败！');
	            }
	        }
	    });
	});
@endif
    
</script>
@stop