<div class="form-group">
    <label for="ad_location_id" class="col-sm-2 control-label">*{{ __('AD Location Id') }}</label>
    <div class="col-sm-6">
       <input type="hidden" name="ad_location_id" value="{{$data->ad_location_id}}">
    <input class="form-control" type="text" readonly adId = "{{$data->ad_location_id}}"   id="ad_location_id" value="{{  $aLocations[$data->ad_location_id]}}" />
    </div>
    <div class="col-sm-4">
        {{ $errors->first('ad_location_id', '<label class="text-danger control-label">:message</label>') }}
    </div>
</div>

<div class="form-group">
    <label for="name" class="col-sm-2 control-label">*{{ __('Role Name') }}</label>
    <div class="col-sm-6">
        <input class="form-control" type="text" name="name" id="name" value="{{$data->name  }}" />
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

    <div class="form-group">
        <label for="content" class="col-sm-2 control-label">*{{ __('Content') }}</label>
        <div class="col-sm-6">
            <input class="form-control" type="text" name="content[]" id="content" value="{{ $data->content }}" />
        </div>
        <div class="col-sm-4">
            {{ $errors->first('content', '<label class="text-danger control-label">:message</label>') }}
        </div>
    </div>
    <div class="form-group">
        <label for="redirect_url" class="col-sm-2 control-label">{{ __('Redirect URL') }}</label>
        <div class="col-sm-6">
            <input class="form-control" type="text" name="ad_url[]" id="redirect_url" value="{{ $data->redirect_url  }}" />
        </div>
        <div class="col-sm-4">
            {{ $errors->first('redirect_url', '<label class="text-danger control-label">:message</label>') }}
        </div>
    </div>

    <div class="form-group  portrait">
            <label class="col-sm-2 control-label" for="portrait">{{ __('Pic Info') }}</label>
            <div class="col-sm-6">
            <input name="oldimg" value="{{  $data->pic_url  }}" type="hidden" >
             <input name="portrait[]" id="portrait"  type="file"  class="form-control" style="padding:5px;" value="">
            </div>
    </div>





