<?php

class FilerobotImage extends \ImageCore
{
    public $url;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'image',
        'primary' => 'id_image',
        'multilang' => true,
        'fields' => [
            'id_product' => ['type' => self::TYPE_INT, 'shop' => 'both', 'validate' => 'isUnsignedId', 'required' => true],
            'position' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'cover' => ['type' => self::TYPE_BOOL, 'allow_null' => true, 'validate' => 'isBool', 'shop' => true],
            'legend' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 128],
            'url' => ['type' => self::TYPE_STRING, 'allow_null' => true,],
        ],
    ];
}