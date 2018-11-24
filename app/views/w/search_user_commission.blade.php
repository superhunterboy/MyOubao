@if ($aSearchFields)
{{ Form::open(array('method' => 'get', 'class' => 'form-inline', 'style' => 'background: #F8F8F8;margin-bottom: 5px;text-align: left;padding: 5px;margin-top: -20px;')) }}
        @if (isset($bWithTrashed) && $bWithTrashed)
        <input type="hidden" name="_withTrashed" value="1" />
        @endif
        <?php
        $i = 0;
        $sCalendarJs = '';
//        $iRowSize = $aSearchConfig['row_size'];
        foreach($aSearchFields as $sField => $aInfo):
//            if ($i % $iRowSize == 0){
//                echo "<tr>\n";
//            }
            $aInputInfo = isset($aInfo['input_info']) ? $aInfo['input_info'] : array();
            $sNodeId = String::camel($sField);
            if ($aInfo['is_date']){;
                $aInfo['type'] = 'date';
                $oFormHelper->dateObjects[] = $sField;
//            die($$sField);
            }
            if ($aInfo['type'] == 'select' && is_string($aInfo['options']) && $aInfo['options']{0} == '$'){
                $sVarName = substr($aInfo['options'],1);
                $sModelName::translateArray($$sVarName);
                $aInfo['options'] = $$sVarName;
//                pr($aInfo['options'])
            }
            $aInfo['div'] = false;
            $aInfo['message'] = false;
            $sLabel = $aInfo['label'];
//            die($sLangPrev);
            $aInfo['label'] = false;
        ?>
            <?php echo $oFormHelper->makeLabel($sField, __($sLangPrev . $sLabel), false, false, false, 'form-group'); ?>
            <div class="form-group">
            <?php echo $oFormHelper->input($sField,isset($$sField) ? $$sField : null,$aInfo); ?>
            </div>
        <?php
//            if ($i++ % $iRowSize == $iRowSize - 1){
//                echo "</tr>\n";
//            }
        endforeach;
//        if ($i % $iRowSize == $iRowSize - 1){
//            echo "\n";
//        }
//        if($i % $iRowSize < $iRowSize - 1){
//            $iSpan = $iRowSize - $i % $iRowSize;
//            $sColSpan = " colspan='$iSpan'";
//        }
        ?>

        <div class="form-group">
              {{ Form::submit(__('_basic.search',null,1), ['class' => 'btn btn-success btn-sm']) }}
              
        </div>
        <div class="form-group" >
        <a href="javascript:void(0)" class="btn btn-embossed btn-danger"
           onclick="batchVerify('<?php $data=[];isset($date)?$data['date']=$date:'' ;isset($date)?$data['commission_type']=$commission_type:'' ;   echo route( 'user-commissions.batch-verify',$data); ?>')">{{__('_function.batch verify')}}</a>
          
        </div>
    <?php
    echo Form::hidden('is_search');
    echo Form::close();
    ?>
@endif
    <?php
    
$modalData1['modal'] = [
    
    'id'      => 'myModal1',
    'title'   => 'Action Confirmation',
    'message' =>__('_function.batch verify'),
    'action'  => 'batch_verify',
    'method'  => 'put',
    'footer'  =>' <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">' . __('Cancel') . '</button>
            <button type="submit" class="btn btn-sm btn-danger">' . __('_function.batch verify'). '</button>',
];
    ?>
    
    @include('w.formModal', $modalData1)
    
@section('end')
{{ script('datetimepicker') }}
{{ script('datetimepicker-zh-CN')}}

<script>


    function batchVerify(href)
        {
//            alert(href);
            $('#batch_verify').attr('action', href);
            $('#myModal1').modal();
            // $('#username').text(username);
        }
        
</script>
@parent
<script type="text/javascript">
    $(function () {
        $('#submitForm').click(function(event) {
            $('#user_search_form').attr('action','/users');
            $('#user_search_form').submit();
        });
        //时间控件
        $('.form_date').datetimepicker({
            language: 'zh-CN',
          weekStart: 1,
            todayBtn: 1,
          autoclose: 1,
          todayHighlight: 1,
          startView: 2,
          minView: 2,
          forceParse: 0,
          showMeridian: 1,
          pickerPosition: 'bottom-left'
        });
    });
</script>

@stop