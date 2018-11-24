
<table width="100%" class="table table-toggle" id="J-table">
    <thead>
        <tr>
            <th>推广渠道名称</th>
            <th>注册人数</th>
            <th>开户类型</th>
            <th>状态</th>
            <th>生成时间</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($datas as $data)
        <tr class=" table-tr-item ">
            <td class="text-left">{{ $data->channel }} <input type="text" class="input w-3" value="{{ $data->url }}" /></td>
            <td><a href="{{ route('user-link-users.index', ['register_link_id' => $data->id]) }}">{{ $data->created_count }}</a></td>
            <td>{{ $data->{$aListColumnMaps['is_agent']} }}</td>
            <td>{{ $data->{$aListColumnMaps['status']} }}</td>
            <td>{{ $data->created_at }}</td>
            <td>
                <a href="{{ route('user-links.view', $data->id) }}">详情</a>
                @if($data->status == 0)
                <a href="javascript:void(0);" url="{{ route('user-links.destroy', $data->id) }}" class="confirmDelete">关闭</a>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>