<div class="help-sider" id="J-help-sider">
    <div class="help-sider-inner">
        <ul class="ul-first">
        @foreach($aTitles as $data)
            <li>
                <a href="#">{{ $data['name'] }}</a>

                <ul class="ul-second">
                   @foreach($data['children'] as $child)
                    <li>
                        <a href="{{route('help.index',$child['category_id'])}}#{{$child['id']}}">{{ $child['title'] }}</a>
                    </li>
                    @endforeach
                </ul>
            </li>
        @endforeach
        </ul>
    </div>
</div>
        