@extends('l.admin', array('active' => $resource))

@section('title')
@parent
{{ $sPageTitle }}
@stop

@section('container')
    @include('w.breadcrumb')
    @include('w.notification')
    @include('w._function_title')

<?php
$oFormHelper->setErrorObject($errors);
?>

{{ Form::open(array( 'class' => 'form-horizontal')) }}

{{ Form::hidden('id', $data->id) }}
    <div class="form-group">
     {{ $oFormHelper->makeLabel(null, __('_sysconfig.item'), false, 'col-sm-2 control-label') }}
        <div class="col-sm-6">
            {{ $oFormHelper->makeLabel( null, $data->item ,false,'control-label text-success') }}
        </div>
    </div>

     <div class="form-group">
        {{ $oFormHelper->makeLabel(null, __('_sysconfig.title') ,false,'col-sm-2 control-label' ) }}
        <div class="col-sm-6">
            {{ $oFormHelper->makeLabel(null, $data->title, false,'control-label text-success') }}
        </div>
    </div>
     <div class="form-group">
        {{ $oFormHelper->makeLabel(null, __('_sysconfig.description') ,false,'col-sm-2 control-label' ) }}
                <div class="col-sm-6">
                    {{ $oFormHelper->makeLabel(null, $data->description, false,'control-label text-success') }}
       </div>
    </div>
     <div class="form-group">
        {{ $oFormHelper->makeLabel(null, __('_sysconfig.default_value') ,false,'col-sm-2 control-label' ) }}
                <div class="col-sm-6">
                    {{ $oFormHelper->makeLabel(null, $data->default_value, false,'control-label text-success') }}
        </div>
    </div>        
	<?php
        switch($data->form_face){
            case 'none':
            case 'text':
                echo $oFormHelper->input('value', $data->value, ['type' => 'text']);
                break;
            case 'edit':
                echo $oFormHelper->input('value', $data->value, ['type' => 'textarea']);
                break;
            case 'multi_select':
                $options['multiple'] = true;
            case 'select':
                $options['options'] = $aDataSource;
                $options['empty'] = true;
                $options['type'] = 'select';
//                pr($options);
//                exit;
                echo $oFormHelper->input('value',$data->value, $options);
                break;
//                $aInitValue = explode(',',$data->value);
//                echo Form::select('value',$aDataSource, $aInitValue, array('value' => $aInitValue, 'multiple' => true));
//                echo Form::hidden('dataSource',$aDataSource);
//                break;
            case 'radio':
                echo $oFormHelper->makeRadio('value', $aDataSource);
//                echo Form::radio('value',$aDataSource,array('value' => $data->value,'legend' => false));
                break;
            case 'checkbox':
                if ($data->validation == 'bool'){
//                    pr($data->value);
//                    exit;
                    echo '<div class="form-group">';
                    
                    echo $oFormHelper->makeLabel("Value",__('Yes'));
                    echo '<div class="col-sm-6">';
                    echo Form::hidden('validation',$data->validation);
                    echo Form::checkbox('value',1,$data->value);
                     echo '</div></div>';
                }
                else{
                    $aInitValue = explode(',',$data->value);
                    $i = 0;
                    foreach($aDataSource as $sValue => $sText){
                        $bCheck = in_array($sValue,$aInitValue);
                        echo Form::checkbox("value[$i]",$sValue,$bCheck);
                        echo $oFormHelper->makeLabel("value[$i]",$sText, false,'form-control');
                        $i++;
                    }
                }
                break;
                
        }
	?>

<div class="form-group">
    <div class="col-sm-offset-2 col-sm-6">
      <a class="btn btn-default" href="{{ route($resource.'.edit', $data->id) }}">{{ __('Reset') }}</a>
      {{ Form::submit(__('Submit'), ['class' => 'btn btn-success']) }}
    </div>
</div>
{{Form::close()}}

@stop

@section('end')
    {{ script('bootstrap-switch') }}
    @parent

@stop
