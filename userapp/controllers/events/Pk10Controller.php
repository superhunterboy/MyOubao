<?php

/**
 * Created by PhpStorm.
 * User: endless
 * Date: 16-5-25
 * Time: 上午11:06
 */
class Pk10Controller extends Controller
{
    public function index(){
        return View::make('events.pk10_agent.index');
    }
}