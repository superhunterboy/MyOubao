@extends('l.sports')


@section ('container')
@include('jc.header')





<div class="layout-main">
    <div class="container">
        <div class="inner">

            <div class="line-list-top"></div>



            <div class="panel-info">

            </div>


            <div class="bet-confirm">
                @include('jc.match', ['datas' => $datas, 'aWays' => $aWayList])
                
                <table class="table table-group">
                    <tr>
                        <th>方案详细</th>
                        <th>过关方式</th>
                        <th>倍数</th>
                        <th>金额</th>
                        <th>彩果</th>
                        <th>奖金</th>
                    </tr>
                    @foreach($aBetDetailList as $data)
                    <tr>
                        <td>{{{ $data->formula }}}</td>
                        <td>{{{ $aDetailWayList[$data->way_id]->name }}}</td>
                        <td>{{{ $data->multiple }}}</td>
                        <td><span class="c-yellow">{{{ number_format($data->amount, 2) }}}</span></td>
                        <td>{{{ $data->formatted_status }}}</td>
                        <td><span class="c-yellow">{{{ number_format($data->prize, 4) }}}</span></td>
                    </tr>
                    @endforeach

                    
                </table>
                
                {{ pagination($aBetDetailList->appends(Input::except('page')), 'w.pages') }}






        </div>
        <div style="height:40px;"></div>
    </div>
</div>
@include('w.footer')
@stop




