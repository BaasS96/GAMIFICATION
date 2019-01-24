<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit97795b1734f1cf1be071f398a5aa61e5
{
    public static $prefixLengthsPsr4 = array (
        'K' => 
        array (
            'Karriere\\JsonDecoder\\' => 21,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Karriere\\JsonDecoder\\' => 
        array (
            0 => __DIR__ . '/..' . '/karriere/json-decoder/src',
        ),
    );

    public static $prefixesPsr0 = array (
        'I' => 
        array (
            'Igorw\\EventSource' => 
            array (
                0 => __DIR__ . '/..' . '/igorw/event-source/src',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit97795b1734f1cf1be071f398a5aa61e5::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit97795b1734f1cf1be071f398a5aa61e5::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit97795b1734f1cf1be071f398a5aa61e5::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}