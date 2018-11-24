



<ul class="field field-type-switch" style="width: 180px;" >
    <li @if(!$type)class="current"@endif><a href="{{ route($resource.'.'.$action,['type'=>0]) }}">彩票</a></li>
{{--     <li @if($type == 1)class="current"@endif><a href="{{ route($resource.'.'.$action,['type'=>1]) }}">竞彩</a></li>--}}
    <li @if($type == 2)class="current"@endif><a href="{{ route($resource.'.'.$action,['type'=>2]) }}">电子娱乐</a></li>
</ul>




