<?php

use Illuminate\Support\Str;

class String extends Str
{
    public static function humenlize($sString){
        return ucwords(str_replace(array('-', '_'), ' ', parent::snake($sString)));
//		return str_replace(' ', '', $value);
    }
}