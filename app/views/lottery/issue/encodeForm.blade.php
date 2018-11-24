
@foreach ($aSeriesLotteries as $key => $oSeriesLotteries)
    <ul  class="list-inline">
        @if (isset($oSeriesLotteries['children']))
            @foreach ($oSeriesLotteries['children'] as $oLottery)
            <li ><a class="btn btn-success" href="{{ route('issues.encode', $oLottery['id']) }}">{{ $oLottery['name'] }}</a></li>
            @endforeach
        @endif
    </ul>
@endforeach

@if ($oLatestIssue)
<table  class="table table-striped table-hover table-bordered">
    <thead style="background: #FFE4E4">
        <tr>
            <th>{{ __($sLangPrev . 'game') }}</th>
            <th>{{ __($sLangPrev . 'issue') }}</th>
            <th>{{ __($sLangPrev . 'draw-periods') }}</th>
            <th>{{ __($sLangPrev . 'status') }} </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{ $aLotteries[$oLatestIssue->lottery_id] }}</td>
            <td>{{ $oLatestIssue->issue }}</td>
            <td>{{ date('Y-m-d H:i:s', $oLatestIssue->begin_time) . ' - ' . $oLatestIssue->end_time2 }}</td>
            <td>{{ $oLatestIssue->formatted_status }}</td>
        </tr>
    </tbody>
</table>

<form action="{{ route('issues.encode') }}" method="post"  class="form-inline" autocomplete="off" style="background: #F8F8F8;margin-bottom: 5px;text-align: left;padding: 5px;">
    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
    <input type="hidden" name="lottery_id" value="{{ $oLatestIssue->lottery_id }}" />
    <input type="text" name="issue" value="{{ $oLatestIssue->issue }}" />


    <label for="wn_number" >{{ __('_issue.wn_number') }}</label>
    <div class="form-group">
          <input type="text" class="form-control" id="wn_number" name="wn_number" value="{{-- Input::old('wn_number') --}}"/>
    </div>
    <div class="form-group">
            <button type="submit" class="btn btn-success">{{ __('Submit') }}</button>
        </div>
</form>
@endif