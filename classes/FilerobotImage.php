<?php
/**
 * MIT License
 *
 * Copyright (c) 2022 Scaleflex
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author   Scaleflex
 *  @copyright 2022 Scaleflex
 *  @license   LICENSE
 *
 * Don't forget to prefix your containers with your own identifier
 * to avoid any conflicts with others containers.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

use ImageCore;

class FilerobotImage extends ImageCore
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
            'url' => ['type' => self::TYPE_STRING, 'allow_null' => true],
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
