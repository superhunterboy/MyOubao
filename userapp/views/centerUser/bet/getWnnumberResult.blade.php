    @foreach ($aIssues as $data)
        <?php
            $iSum = array_sum(str_split($data->wn_number));
            $sBigSmall = $iSum >= 0 && $iSum < 14 ? '小' : '大';
            $sOddEven = $iSum % 2 == 0 ? '双' : '单';
            switch($iSum){
                case $iSum >= 0 && $iSum <= 5 : $sExtremum = '极小';break;
                case $iSum >= 22 && $iSum <= 27 : $sExtremum = '极大';break;
                default : $sExtremum = '--';
            }
        ?>
        <ul>
            <li> {{ $data->issue }} </li>
            <li> {{ date('Y-m-d H:i:s', $data->offical_time) }} </li>
            <li> {{ $data->wn_number }} </li>
            <li class="hezhi"> {{ $iSum }} </li>
            <li> {{ $sBigSmall }} </li>
            <li> {{ $sOddEven }} </li>
            <li> {{ $sExtremum }} </li>

        </ul>
    @endforeach

@section('end')

@show
