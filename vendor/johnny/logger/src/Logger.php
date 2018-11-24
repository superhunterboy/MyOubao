<?php namespace Johnny\Logger;
/**
 * Class Logger - Description
 *
 * @package Illuminate\Support\Facades\Logger
 * @author Johnny <Johnny.joyful@gmail.com>
 */
class Logger extends \Illuminate\Support\Facades\Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'logger'; }
}