<div class="form-group">
    <label for="type_id" class="col-sm-3 control-label">{{ __('_msgmessage.type_id') }}</label>
    <div class="col-sm-5">
        <select class="form-control" name="type_id" id="type_id" >
            @foreach ($aMsgTypes as $id => $title)
                <option value="{{ $id }}" {{ Input::old('type_id') == $id ? 'selected' : ''}}>{{ $title }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-sm-4">
        {{ $errors->first('type_id', '<label class="text-danger control-label">:message</label>') }}
    </div>
</div>

<div class="form-group">
    <label for="title" class="col-sm-3 control-label">{{ __('_msgmessage.title') }}</label>
    <div class="col-sm-5">
      <input type="text" class="form-control" id="title" name="title" value="{{ Input::old('title') }}"/>
    </div>
    <div class="col-sm-4">
        {{ $errors->first('title', '<label class="text-danger control-label">:message</label>') }}
    </div>
</div>
@if ($iReceiverType == 1)
<div class="form-group">
    <label for="receiver" class="col-sm-3 control-label">{{ __('_msguser.receiver') }}</label>
    <div class="col-sm-5">
        <input class="form-control" type="text" name="receiver" id="receiver" value="{{ Input::old('receiver') }}" />
        <p>每个接收者用英文逗号[,]隔开, 英文分号[;], 英文空格[ ], 如果上传人员名单, 则文件格式必须是.txt格式</p>
        <input type="file" name="file" onchange="showReceiverList(this)">
    </div>

    <div class="col-sm-4">
        {{ $errors->first('receiver', '<label class="text-danger control-label">:message</label>') }}
    </div>
</div>
@endif
<div class="form-group">
    <label for="is_keep" class="col-sm-3 control-label">{{ __('_msguser.is_keep') }}</label>
    <div class="col-sm-5">
        <div class="switch " data-on-label="{{ __('Yes') }}"  data-off-label="{{ __('No') }}">
            <input type="checkbox" name="is_keep" id="is_keep" value="1"
                {{ Input::old('is_keep') == 1 ? 'checked': '' }}>
        </div>
    </div>
    <div class="col-sm-4">
        {{ $errors->first('is_keep', '<label class="text-danger control-label">:message</label>') }}
    </div>
</div>
@if ($iReceiverType != 3)
<div class="form-group">
    <label class="col-sm-3 control-label">{{ __('_msgmessage.send-type') }}</label>
    <div class="col-sm-5">
        @if ($iReceiverType == 1)
        <input type="checkbox" name="not_self" id="not_self" value="1" {{ Input::old('not_self') == 1 ? 'checked': '' }}>{{ __('_msgmessage.not-self') }}
        <input type="checkbox" name="all_children" id="all_children" value="1" {{ Input::old('all_children') == 1 ? 'checked': '' }}>{{ __('_msgmessage.all-children') }}
        <input type="checkbox" name="direct_children" id="direct_children" value="1" {{ Input::old('direct_children') == 1 ? 'checked': '' }}>{{ __('_msgmessage.direct-children') }}
        <input type="checkbox" name="all_parent" id="all_parent" value="1" {{ Input::old('all_parent') == 1 ? 'checked': '' }}>{{ __('_msgmessage.all-parents') }}
        @elseif ($iReceiverType == 2)
        <input type="radio" name="agent_level" value="0" {{ Input::old('agent_level') == 0 ? 'checked': '' }}>{{ __('_msgmessage.all-agents') }}
        <input type="radio" name="agent_level" value="1" {{ Input::old('agent_level') == 1 ? 'checked': '' }}>{{ __('_msgmessage.top-agents') }}
        <input type="radio" name="agent_level" value="2" {{ Input::old('agent_level') == 2 ? 'checked': '' }}>{{ __('_msgmessage.normal-agents') }}
        @endif
    </div>
</div>
@endif
<div class="form-group">
    <label for="content" class="col-sm-3 control-label">{{ __('_msgmessage.content') }}</label>
    <div class="col-sm-5">
        <textarea class="form-control" type="text" name="content" id="content" >{{ Input::old('content') }}</textarea>
    </div>
    <div class="col-sm-4">
        {{ $errors->first('content', '<label class="text-danger control-label">:message</label>') }}
    </div>
</div>


