<?php

namespace CronixWeb\Mailgun\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \CronixWeb\Mailgun\Mailgun
 */
class Mailgun extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \CronixWeb\Mailgun\Mailgun::class;
    }
}
