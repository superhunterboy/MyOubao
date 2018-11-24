<div class="form-group">
    <label for="ad_location_id" class="col-sm-2 control-label">*{{ __('AD Location Id') }}</label>
    <div class="col-sm-6">
        <select class="form-control" name="ad_location_id" id="ad_location_id" >
            <option value=""></option>
            @foreach ($aLocationsId as  $value)
                 <option value="{{$value->id }}" aType="{{$value->type_name}}" >{{ $value->name  }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-sm-4">
        {{ $errors->first('ad_location_id', '<label class="text-danger control-label">:message</label>') }}
    </div>
</div>

<div class="form-group">
    <label for="name" class="col-sm-2 control-label">*{{ __('Role Name') }}</label>
    <div class="col-sm-6">
        <input class="form-control" type="text" name="name" id="name" value="{{ Input::old('name', isset($data) ? $data->name : '') }}" />
    </div>
    <div class="col-sm-4">
        {{ $errors->first('name', '<label class="text-danger control-label">:message</label>') }}
    </div>
</div>


<div class="form-group">
    <label for="is_closed" class="col-sm-2 control-label">*{{ __('Is Closed') }}</label>
    <div class="col-sm-6">
        <div class="switch " data-on-label="{{ __('Yes') }}"  data-off-label="{{ __('No') }}">
            <input type="checkbox" name="is_closed" id="is_closed" value="1"
                {{ Input::old('is_closed', (isset($data) ? $data->is_closed : 0 )) ? 'checked': '' }}>
        </div>
    </div>
    <div class="col-sm-4">
        {{ $errors->first('is_closed', '<label class="text-danger control-label">:message</label>') }}
    </div>
</div>

<div class="text-box" style="display:none;">
    <div class="form-group">
        <label for="content" class="col-sm-2 control-label">*{{ __('Content') }}</label>
        <div class="col-sm-6">
            <input class="form-control" type="text" name="content[]" id="content" value="" />
        </div>
        <div class="col-sm-4">
            {{ $errors->first('content', '<label class="text-danger control-label">:message</label>') }}
        </div>
    </div>
    <div class="form-group">
        <label for="redirect_url" class="col-sm-2 control-label">{{ __('Redirect URL') }}</label>
        <div class="col-sm-6">
           <input class="form-control" type="text" name="ad_url[]" id="redirect_url" value="" />
        </div>
        <div class="col-sm-4">
            {{ $errors->first('redirect_url', '<label class="text-danger control-label">:message</label>') }}
        </div>
    </div>
</div>


<div class="pic-box" style="display:none;">
    <div class="form-group ">
            <label class="col-sm-2 control-label">{{ __('Pic') }}</label>
            <div class="col-sm-6">
               <input name="portrait[]" type="file" class="form-control" style="padding:5px;">
            </div>
    </div>
</div>

<div class="pics-box" style="display:none;">
    <div class="form-group">
        <label for="portrait" class="col-sm-2 control-label">{{ __('Pic Info') }}</label>
        <div class="col-sm-6" name="file-img">
            <div class="row">
                <div class="col-sm-10" >
                    <div class="row">
                        <div class="col-sm-6" style="margin-bottom:5px;">
                            <input id="portrait" name="portrait[]" type="file" class="form-control" style="padding:5px;">
                        </div>
                        <div class="col-sm-6"><input class="form-control" name="ad_url[]" type="text"  placeholder="*{{ __('AD URL') }}" value=""  /></div>
                        <div class="col-sm-12">
                            <input class="form-control" type="text" name="content[]" id="content"   placeholder="*{{ __('Content') }}" value="" />
                        </div>
                    </div>
                </div>
                <div class="col-sm-2">
                    <a class="btn btn-default btn-block" name="plus-img" href="javascript:void(0);"><i class="glyphicon glyphicon-plus"></i> </a>
                </div>
            </div>
        </div>
    </div>
</div>







