<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit59057821eb04469a56bff79213e671a8
{
    public static $files = array (
        '3d0e88aad197c988a28b1c624cf13e32' => __DIR__ . '/../..' . '/src/post_types.php',
        '00d09953df7bec6b90a30eac4b77d7fc' => __DIR__ . '/../..' . '/src/settings.php',
    );

    public static $prefixLengthsPsr4 = array (
        'W' => 
        array (
            'Wizhi\\' => 6,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Wizhi\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit59057821eb04469a56bff79213e671a8::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit59057821eb04469a56bff79213e671a8::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
