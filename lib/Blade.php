<?php

use eftec\bladeone\BladeOne;


/**
 * It's a static wrapper/container for Bladeone.<br>
 * This class is used for compatibility with Laravel's blade<br>
 * Example: You could call Blade::function() instead of $blade->function()<br>
 * -This class is optional and it's not recommended.<br>
 * -Why?<br>
 * -Because it's hard to debug and the IDE couldn't recognize the methods.<br>
 * Class Blade
 * @package eftec\bladeone
 * @author   Jorge Patricio Castro Castillo <jcastro arroba eftec dot cl>
 * @version 1 2018-08-17
 * @link https://github.com/EFTEC/BladeOne
 */
class Blade
{
    /** @var BladeOne container of the class */
    public static $_instance;

    /**
     * Converts a static call into a dynamic call.
     * @param  string $function
     * @param  array $parameters
     * @return mixed
     * @throws Exception
     */
    public static function __callStatic($function, $parameters)
    {
        $instance = static::$_instance;
        if (! $instance) {
            throw new Exception('The object BladeOne is missing, you must create it');
        }
        return $instance->$function(...$parameters);
    }
}
/**
 * BladeOne - A Blade Template implementation in a single file
 * Copyright (c) 2016-2018 Jorge Patricio Castro Castillo MIT License. Don't delete this comment, its part of the license.
 * Part of this code is based in the work of Laravel PHP Components.
 */