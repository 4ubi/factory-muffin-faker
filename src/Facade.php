<?php

/*
 * This file is part of Factory Muffin Faker.
 *
 * (c) Graham Campbell <graham@alt-three.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\FactoryMuffin\Faker;

/**
 * This is the faker facade class.
 *
 * This class dynamically proxies static method calls to the underlying faker.
 *
 * @see League\FactoryMuffin\Faker\Faker
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class Facade
{
    /**
     * The underlying faker instance.
     *
     * @var Faker
     */
    private static $instance;

    /**
     * Get the underlying faker instance.
     *
     * We'll always cache the instance and reuse it.
     *
     * @return Faker
     */
    public static function instance(): Faker
    {
        if (!self::$instance) {
            self::$instance = new Faker();
        }

        return self::$instance;
    }

    /**
     * Reset the underlying faker instance.
     *
     * @return Faker
     */
    public static function reset(): Faker
    {
        self::$instance = null;

        return self::instance();
    }

    /**
     * Handle dynamic, static calls to the object.
     *
     * @codeCoverageIgnore
     *
     * @param string $method    The method name.
     * @param array $arguments The arguments.
     *
     * @return mixed
     */
    public static function __callStatic(string $method, array $arguments)
    {
        return match (count($arguments)) {
            0 => self::instance()->$method(),
            1 => self::instance()->$method($arguments[0]),
            2 => self::instance()->$method($arguments[0], $arguments[1]),
            3 => self::instance()->$method($arguments[0], $arguments[1], $arguments[2]),
            default => call_user_func_array([self::instance(), $method], $arguments),
        };
    }
}
