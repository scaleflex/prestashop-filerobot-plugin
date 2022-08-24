<?php
/**
 *  2022 Scaleflex
 *
 *  NOTICE OF LICENSE
 *
 *  This source file is subject to the Academic Free License (AFL 3.0)
 *  that is bundled with this package in the file LICENSE.txt.
 *  It is also available through the world-wide-web at this URL:
 *  http://opensource.org/licenses/afl-3.0.php
 *  If you did not receive a copy of the license and are unable to
 *  obtain it through the world-wide-web, please send an email
 *  to license@prestashop.com so we can send you a copy immediately.
 *
 *  DISCLAIMER
 *
 *  Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 *  versions in the future. If you wish to customize PrestaShop for your
 *  needs please refer to http://www.prestashop.com for more information.
 *
 * @author 2022 Scaleflex
 * @copyright Scaleflex
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

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
            'id_product' => ['type' => self::TYPE_INT,
                'shop' => 'both',
                'validate' => 'isUnsignedId',
                'required' => true],
            'position' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'cover' => ['type' => self::TYPE_BOOL, 'allow_null' => true, 'validate' => 'isBool', 'shop' => true],
            'legend' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 128],
            'url' => ['type' => self::TYPE_STRING, 'allow_null' => true,],
        ],
    ];


    public static function genrateUrl($url, $width, $height)
    {
        $url = parse_url($url);
        parse_str($url['query'], $query);
        $query['width'] = $width;
        $query['height'] = $height;
        $url['query'] = http_build_query($query);
        return $url['scheme'] . '://' . $url['host'] . $url['path'] . '?' . $url['query'];

    }
}
