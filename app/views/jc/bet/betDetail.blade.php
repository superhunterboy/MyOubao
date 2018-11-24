
<table class="table table-striped table-hover table-bordered text-center">
    <thead>
    <tr>
        <th class="text-center">序号</th>
        <th class="text-center">关数</th>
        <th class="text-center">明细</th>
        <th class="text-center">中奖明细</th>
        <th class="text-center">奖金</th>
        <th class="text-center">彩果</th>
    </tr>
    </thead>
    <tbody>
    @if(isset($aDetail['bet_detail']))
    <?php $i = 1;?>
    @foreach($aDetail['bet_detail'] as $betsDetail)
    <tr {{implode(' ',$betsDetail['a'])}}>
        @if(isset($betsDetail['detail']) &&
        isset($betsDetail['odds']) &&
        isset($aDetail['single_amount']) &&
        isset($aDetail['multiple']) &&
        isset($betsDetail['prize'])
        )
        <td>{{$i}}</td>
        <td>{{count($betsDetail['detail'])}}</td>
        <td>{{implode(' X ',$betsDetail['detail'])}}</td>
        <td>{{implode(' X ',$betsDetail['odds'])}} X {{$aDetail['single_amount']}} X {{$aDetail['multiple']}} = {{$betsDetail['prize']}}</td>
        <td>{{$betsDetail['real_prize']}}</td>
        <td>{{__('jc/_manjcbet.' . strtolower(\Str::slug($aStatus[$betsDetail['status']])))}}</td>
        @endif
    </tr>
    <?php $i++?>
    @endforeach
    @endif
    </tbody>
</table>
