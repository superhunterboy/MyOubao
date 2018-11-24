<?php

class FormHelper
{
    public $classes = [];
    public $errors;
//    public $columnSettings;
//    public $attributes;
    public $modelName;
    protected $model;
    public $dateObjects;
    public $langPrev;

    public function setClass($aClasses){
        $this->classes = $aClasses;
    }

    public function setLangPrev($sLangPrev){
        $this->langPrev = $sLangPrev;
    }
    
    public function setModel($model){
        $this->model = $model;
    }

    public function setErrorObject($oErrors){
        $this->errors = $oErrors;
    }

    public function input($name, $value = null, $options = []){
        $sModelName = get_class($this->model);
//        !is_null($value) or $value = $this->model->$name;
        $type = isset($options['type']) ? $options['type'] : $this->model->columnSettings[$name]['form_type'];
        // label
        $bLabel = (isset($options['label']) && !$options['label']) ? false : true;
        // empty for select object
        $sEmpty = isset($options['empty']) ? $options['empty'] : false;
        // todo: get require
        $bDiv = (isset($options['div']) && !$options['div']) ? false : true;
        $bMessage = (isset($options['message']) && !$options['message']) ? false : true;
        $sHtml = '';
        !$bDiv or $sHtml .= $this->openDiv($this->classes['div']);
        if ($bLabel){
            $sLabel = isset($options['label']) ? $options['label'] : $name;
            $sLabel = __($this->langPrev . $sLabel, null, 2);
            $bRequired = isset($this->model->columnSettings[$name]['required']) ? $this->model->columnSettings[$name]['required'] : false;
            $sHtml .= $this->makeLabel($name, $sLabel, $bRequired);
        }
        !$bDiv or $sHtml .= $this->openDiv($this->classes['input_div']);
//        pr($name);
//        die($type);
        switch($type){
            case 'text':
                isset($options['class']) or $options['class'] = $this->classes['input'];
                $sHtml .= Form::input($type, $name, $value, $options);
                break;
            case 'textarea':
                $sHtml .= Form::textarea($name, $value, $options);
                break;
            case 'select':
                isset($options['class']) or $options['class'] = $this->classes['select'];
                if (isset($options['empty'])){
//                    if (is_array($options['empty'])){
//                        $aEmpty = each($options['empty']);
//                        $sEmptyKey = & $aEmpty['key'];
//                        $sEmptyValue = & $aEmpty['value'];
//                    }
                    $sEmptyValue = $options['empty'] === true ? null : $options['empty'];
                    $aSelectOptions[null] = $sEmptyValue;
                    foreach($options['options'] as $sTmpkey => $sTmpValue){
                        $aSelectOptions[$sTmpkey] = $sTmpValue;
                    }
                    unset($sTmpkey, $sTmpValue);
                }
                else{
                    $aSelectOptions = & $options['options'];
                }
//                pr($aSelectOptions);
//                exit;
//                if ($options
                $aOptions = ['id' => $name, 'class' => 'form-control select-sm'];
//                $options['multiple'] = true;
                if (isset($options['multiple']) && $options['multiple']){
//                    $aOptions['multiple'] = true;
//                    pr($aSelectOptions);
                    !is_null($value) or $value = $this->model->$name;
                    $aValues = $value ? explode(',',$value) : [];
//                    pr($aValues);
//                    pr($options);
                    $old_model = $this->model;
                    unset($this->model);
                    foreach($aSelectOptions as $v => $text){
                        if ($v === '') continue;
                        $oTmpOptions = in_array($v,$aValues) ? ['checked' => 'checked'] : [];
//                        $sHtml .= '<li>' . Form::checkbox($name . '[]', $v, false, $oTmpOptions) . ' ' . $text . '</li>';
                        $sHtml .= '<li>' . Form::input('checkbox', $name . '[]', $v, $oTmpOptions) . ' ' . $text . '</li>';
                    }
                    $this->model = $old_model;
                }
                else{
                    $sHtml .= Form::select($name, $aSelectOptions,$value,$aOptions);
                }
                break;
            case 'bool':
                isset($options['class']) or $options['class'] = $this->classes['radio'];
                $sHtml .= $this->makeRadio($name, $value, $options);
                break;
            case 'date':
//                die($value);
                $sHtml .= $this->makeDate($name,$value,$options);
                break;
            case 'datetime':
//                die($value);
                $sHtml .= $this->makeDatetime($name,$value,$options);
                break;
//                die($sHtml);
        }
        !$bDiv or $sHtml .= $this->closeDiv();
        !$bMessage or $sHtml .= $this->makeMessage($name);
        !$bDiv or $sHtml .= $this->closeDiv();
//                die($sHtml);
        return $sHtml;
    }

    function makeRadio($name, $value, $options = []){
        $sHtml = '<div class="' . $this->classes['radio_div'] . '" data-on-label="' .  __('Yes') . '"  data-off-label="' . __('No') . '">';
        $sHtml .= Form::checkbox($name,1,$value,$options);
        $sHtml .= '</div>';
        return $sHtml;
    }

    function makeDate($name, $value, $options){
//        $this->openDiv($sClass)
//        die($value);
        $sHtml = '<div class="' . $this->classes['date'] . '" data-date="' . $value . '" data-date-format="yyyy-mm-dd" data-link-format="yyyy-mm-dd" data-link-field="' . $name . '" style="width:215px">';
        $sHtml .= '<input class="form-control" size="16" type="text" value="' . $value . '">
        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span></div>';
        $sHtml .= '<input type="hidden" id="' . $name . '" name="' . $name . '" value="' . $value . '" />';
        return $sHtml;
    }
    
    
    function makeDatetime($name, $value, $options){
        $sHtml = '<div class="' . $this->classes['date'] . '" data-date="' . $value . '" data-date-format="yyyy-mm-dd hh:ii" data-link-format="yyyy-mm-dd hh:ii" data-link-field="' . $name . '" style="width:215px">';
        $sHtml .= '<input class="form-control" size="16" type="text" value="' . $value . '">
        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span></div>';
        $sHtml .= '<input type="hidden" id="' . $name . '" name="' . $name . '" value="' . $value . '" />';
        return $sHtml;
    }

    function makeLabel($sFor, $sLable, $bRequired = false, $sClass = null, $bDiv = false, $sDivClass = null){
//        $sHtml = '<label for="' . $sFor . '"';
        $sHtml = $bDiv ? $this->openDiv($sDivClass) : '';
        !is_null($sClass) or $sClass = $this->classes['label'];
        $sRequired = $bRequired ? '*' : '';
        $sHtml .= Form::label($sFor, $sRequired . __(String::humenlize($sLable)), ['class' => $sClass]);
        !$bDiv or $sHtml .= $this->closeDiv();
//        pr($sHtml);
//        exit;
        return $sHtml;
    }

    function makeInput(){


    }

    function makeMessage($sObjName){
// <div class="col-sm-4">
//        {{ $errors->first('parent_id', '<label class="text-danger control-label">:message</label>') }}
//    </div>
        $sHtml = $this->openDiv($this->classes['msg_div']);
        $sLabelHtml = $this->makeLabel($sObjName, ':message', false, $this->classes['msg_label'], false);
//        pr($this->errors);
        $sHtml .= $this->errors->first($sObjName, $sLabelHtml);
        $sHtml .= $this->closeDiv();
        return "\n" . $sHtml . "\n";
    }

    function openDiv($sClass = null){
        $sHtml = "\n" . '<DIV';
        empty($sClass) or $sHtml .= ' class="' . $sClass . '"';
        $sHtml .= '>';
        return $sHtml . "\n";
    }

    function closeDiv(){
        return '</DIV>' . "\n";
    }
}