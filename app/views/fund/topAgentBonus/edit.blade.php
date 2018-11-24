@extends('l.admin', array('active' => $resource))

@section('title')
@parent
{{ $sPageTitle }}
@stop

@section('container')
<div class="col-md-12">
    @include('w.breadcrumb')
    @include('w._function_title')
    @include('w.notification')

    <div class="panel panel-default">
        <div class="panel-body">
            <?php
            $aAttributes = $isEdit ? $data->getAttributes() : array_combine($aOriginalColumns, array_fill(0, count($aOriginalColumns), null));

            if (!$isEdit) {
                foreach ($aInitAttributes as $sColumn => $mValue) {
                    $data->$sColumn = $mValue;
                }
            }
            $oFormHelper->setErrorObject($errors);
            ?>
            {{ Form::model($data, array('method' => 'post', 'class' => 'form-horizontal')) }}
            @if ($isEdit)
            <input type="hidden" name="_method" value="PUT" />
            @endif
            <?php
            echo $oFormHelper->input('username', null, ['id' => 'username', 'class' => 'form-control', 'disabled'=>'disabled']);
            echo $oFormHelper->input('bonus_percent', null, ['id' => 'bonus_percent', 'class' => 'form-control', 'options' => $aBonusPercents, 'empty' => true]);
            echo $oFormHelper->input('turnover_limit', null, ['id' => 'turnover_limit', 'class' => 'form-control']);
            echo $oFormHelper->input('max_salary', null, ['id' => 'max_salary', 'class' => 'form-control']);
            echo $oFormHelper->input('active_user_count', null, ['id' => 'active_user_count', 'class' => 'form-control']);
            ?>
            <div class = "form-group">
                <div class = "col-sm-offset-3 col-sm-5">
                    <a class = "btn btn-default" href = "{{ route($resource. ($isEdit ? '.edit' : '.create'), $data->id) }}">{{ __('Reset') }}</a>
                    {{ Form::submit(__('Submit'), ['class' => 'btn btn-success']) }}
                </div>
            </div>
            {{Form::close()}}
        </div>
    </div>
</div>
@stop

@section('end')

@parent

<script>
    function modal(href)
    {
        $('#real-delete').attr('action', href);
        $('#myModal').modal();
    }
</script>
@stop
