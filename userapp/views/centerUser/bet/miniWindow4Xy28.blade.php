@foreach ($datas as $data)


<ul>
    <li class="num-1"> {{ $data->created_at }} </li>
    <li class="num-3"> {{ $aLotteries[$data->lottery_id] }} </li>
    <li class="num-4">
        {{ $data->serial_number }}
    </li>
    <li class="num-5"> {{ $data->issue }} </li>
    <li class="num-6">
        @if ( strlen( $data->display_bet_number) > 10 )
        <a class="view-detail" href="javascript:void(0);">详细号码</a><textarea class="data-textarea" style="display:none;">{{ $data->display_bet_number }} </textarea>
        @else
        {{ $data->display_bet_number }}
        @endif
    </li>
    <li class="num-7">{{ number_format($data->amount, 2) }} </li>
    <li class="num-8">{{ $data->prize_formatted ? number_format($data->prize, 2) : '--' }} </li>
    <li class="num-9">{{ $data->formatted_status }} </li>
    <li class="num-10">

        @if ($data->status == Project::STATUS_NORMAL)
        <!--<input type="hidden" name="zd-id" id="zd-id" />-->

        <div class="cancel" value="{{ route('projects.drop',['id' => $data->id]) }}/0" href="javascript:void(0);"></div>
        @else
        --
        @endif
    </li>
    <!--<li class="num-10"><span></span></li>-->
</ul>
@endforeach

@section('end')

@show