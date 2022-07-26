<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInitcd17b2c16c5cca8ff5c3387d9f1941c7
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        spl_autoload_register(array('ComposerAutoloaderInitcd17b2c16c5cca8ff5c3387d9f1941c7', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInitcd17b2c16c5cca8ff5c3387d9f1941c7', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInitcd17b2c16c5cca8ff5c3387d9f1941c7::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
