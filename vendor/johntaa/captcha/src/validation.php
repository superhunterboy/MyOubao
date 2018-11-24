<?php

Validator::extend('captcha', function($attribute, $value, $parameters)
{
    // $value = Str::lower($value);
    // pr($value);
    // pr('-----------');
    // $captchaHash = Session::get('captchaHash');
    // pr($captchaHash);
    // pr('-----------');
    // $hashed = Hash::make($value);
    // pr(Hash::check($value, $hashed));
    // pr('-----------');
    // pr(Hash::check($value, $captchaHash));
    // pr(Captcha::check($value));
    // exit;
    return Captcha::check($value);
});
