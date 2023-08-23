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

namespace Scaleflex\PrestashopFilerobot\Adapter;

use Image;
use Language;
use Product;

class FilerobotImageRetriever
{
    /**
     * @var \Link
     */
    private $link;

    public function __construct(\Link $link)
    {
        $this->link = $link;
    }

    /**
     * @param array $product
     * @param \Language $language
     *
     * @return array
     */
    public function getAllProductImages(array $product, \Language $language)
    {
        $productInstance = new \Product(
            $product['id_product'],
            false,
            $language->id
        );

        $images = $this->getImages($productInstance->id, $language->id);

        if (empty($images)) {
            return [];
        }

        $combinationImages = $productInstance->getCombinationImages($language->id);
        if (!$combinationImages) {
            $combinationImages = [];
        }
        $imageToCombinations = [];

        foreach ($combinationImages as $imgs) {
            foreach ($imgs as $img) {
                $imageToCombinations[$img['id_image']][] = $img['id_product_attribute'];
            }
        }

        $images = array_map(function (array $image) use (
            $productInstance,
            $imageToCombinations
        ) {
            $image = array_merge($this->getImage(
                $productInstance,
                $image['id_image'],
                $image['url']
            ), $image);

            if (isset($imageToCombinations[$image['id_image']])) {
                $image['associatedVariants'] = $imageToCombinations[$image['id_image']];
            } else {
                $image['associatedVariants'] = [];
            }

            return $image;
        }, $images);

        return $images;
    }

    /**
     * @param array $product
     * @param \Language $language
     *
     * @return array
     */
    public function getProductImages(array $product, \Language $language)
    {
        $images = $this->getAllProductImages($product, $language);

        $productAttributeId = $product['id_product_attribute'];
        $filteredImages = [];

        foreach ($images as $image) {
            if (in_array($productAttributeId, $image['associatedVariants'])) {
                $filteredImages[] = $image;
            }
        }

        return (0 === count($filteredImages)) ? $images : $filteredImages;
    }

    /**
     * @param \Product|\Store|\Category $object
     * @param int $id_image
     *
     * @return array|null
     *
     * @throws \PrestaShopDatabaseException
     */
    public function getImage($object, $id_image, $filerobotUrl)
    {
        if (!$id_image) {
            return null;
        }

        if ('Product' === $object::class) {
            $type = 'products';
            $getImageURL = 'getImageLink';
            $root = _PS_PROD_IMG_DIR_;
            $imageFolderPath = implode(DIRECTORY_SEPARATOR, [
                rtrim($root, DIRECTORY_SEPARATOR),
                rtrim(\Image::getImgFolderStatic($id_image), DIRECTORY_SEPARATOR),
            ]);
        } elseif ('Store' === $object::class) {
            $type = 'stores';
            $getImageURL = 'getStoreImageLink';
            $root = _PS_STORE_IMG_DIR_;
            $imageFolderPath = rtrim($root, DIRECTORY_SEPARATOR);
        } else {
            $type = 'categories';
            $getImageURL = 'getCatImageLink';
            $root = _PS_CAT_IMG_DIR_;
            $imageFolderPath = rtrim($root, DIRECTORY_SEPARATOR);
        }

        $urls = [];
        $image_types = \ImageType::getImagesTypes($type, true);

        $extPath = $imageFolderPath . DIRECTORY_SEPARATOR . 'fileType';
        $ext = Tools::file_get_contents($extPath) ?: 'jpg';

        $mainImagePath = implode(DIRECTORY_SEPARATOR, [
            $imageFolderPath,
            $id_image . '.' . $ext,
        ]);
        $generateHighDpiImages = (bool) \Configuration::get('PS_HIGHT_DPI');

        foreach ($image_types as $image_type) {
            if (!$filerobotUrl) {
                $resizedImagePath = implode(DIRECTORY_SEPARATOR, [
                    $imageFolderPath,
                    $id_image . '-' . $image_type['name'] . '.' . $ext,
                ]);

                if (!file_exists($resizedImagePath)) {
                    \ImageManager::resize(
                        $mainImagePath,
                        $resizedImagePath,
                        (int) $image_type['width'],
                        (int) $image_type['height']
                    );
                }

                if ($generateHighDpiImages) {
                    $resizedImagePathHighDpi = implode(DIRECTORY_SEPARATOR, [
                        $imageFolderPath,
                        $id_image . '-' . $image_type['name'] . '2x.' . $ext,
                    ]);
                    if (!file_exists($resizedImagePathHighDpi)) {
                        \ImageManager::resize(
                            $mainImagePath,
                            $resizedImagePathHighDpi,
                            (int) $image_type['width'] * 2,
                            (int) $image_type['height'] * 2
                        );
                    }
                }

                $url = $this->link->$getImageURL(
                    isset($object->link_rewrite) ? $object->link_rewrite : $object->name,
                    $id_image,
                    $image_type['name']
                );
            } else {
                $url = \FilerobotImage::genrateUrl($filerobotUrl, $image_type['width'], $image_type['height']);
            }

            $urls[$image_type['name']] = [
                'url' => $url,
                'width' => (int) $image_type['width'],
                'height' => (int) $image_type['height'],
            ];
        }

        uasort($urls, function (array $a, array $b) {
            return $a['width'] * $a['height'] > $b['width'] * $b['height'] ? 1 : -1;
        });

        $keys = array_keys($urls);

        $small = $urls[$keys[0]];
        $large = end($urls);
        $medium = $urls[$keys[ceil((count($keys) - 1) / 2)]];

        return [
            'bySize' => $urls,
            'small' => $small,
            'medium' => $medium,
            'large' => $large,
            'legend' => isset($object->meta_title) ? $object->meta_title : $object->name,
            'id_image' => $id_image,
        ];
    }

    /**
     * @param string $imageHash
     *
     * @return array
     */
    public function getCustomizationImage($imageHash)
    {
        $large_image_url = rtrim($this->link->getBaseLink(), '/') . '/upload/' . $imageHash;
        $small_image_url = $large_image_url . '_small';

        $small = [
            'url' => $small_image_url,
        ];

        $large = [
            'url' => $large_image_url,
        ];

        $medium = $large;

        return [
            'bySize' => [
                'small' => $small,
                'medium' => $medium,
                'large' => $large,
            ],
            'small' => $small,
            'medium' => $medium,
            'large' => $large,
            'legend' => '',
        ];
    }

    /**
     * @param \Language $language
     *
     * @return array
     *
     * @throws \PrestaShopDatabaseException
     */
    public function getNoPictureImage(\Language $language)
    {
        $urls = [];
        $type = 'products';
        $image_types = \ImageType::getImagesTypes($type, true);

        foreach ($image_types as $image_type) {
            $url = $this->link->getImageLink(
                '',
                $language->iso_code . '-default',
                $image_type['name']
            );

            $urls[$image_type['name']] = [
                'url' => $url,
                'width' => (int) $image_type['width'],
                'height' => (int) $image_type['height'],
            ];
        }

        uasort($urls, function (array $a, array $b) {
            return $a['width'] * $a['height'] > $b['width'] * $b['height'] ? 1 : -1;
        });

        $keys = array_keys($urls);

        $small = $urls[$keys[0]];
        $large = end($urls);
        $medium = $urls[$keys[ceil((count($keys) - 1) / 2)]];

        return [
            'bySize' => $urls,
            'small' => $small,
            'medium' => $medium,
            'large' => $large,
            'legend' => '',
        ];
    }

    /**
     * Get product images and legends.
     *
     * @param int $id_lang Language identifier
     * @param \Context|null $context
     *
     * @return array Product images and legends
     */
    public function getOneImage($id_image, $id_lang)
    {
        return \Db::getInstance()->executeS(
            '
            SELECT image_shop.`cover`, i.`id_image`, il.`legend`, i.`position`, i.`url`
            FROM `' . _DB_PREFIX_ . 'image` i
            ' . \Shop::addSqlAssociation('image', 'i') . '
            LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int) $id_lang . ')
            WHERE i.`id_image` = ' . (int) $id_image
        );
    }

    /**
     * Get product images and legends.
     *
     * @param int $id_lang Language identifier
     * @param \Context|null $context
     *
     * @return array Product images and legends
     */
    public function getImages($id_product, $id_lang, \Context $context = null)
    {
        return \Db::getInstance()->executeS(
            '
            SELECT image_shop.`cover`, i.`id_image`, il.`legend`, i.`position`, i.`url`
            FROM `' . _DB_PREFIX_ . 'image` i
            ' . \Shop::addSqlAssociation('image', 'i') . '
            LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int) $id_lang . ')
            WHERE i.`id_product` = ' . (int) $id_product . '
            ORDER BY `position`'
        );
    }

    /**
     * @param int $id_lang Language identifier
     *
     * @return array|false
     */
    public function getCombinationImages($id_product, $id_lang)
    {
        if (!\Combination::isFeatureActive()) {
            return false;
        }

        $product_attributes = \Db::getInstance()->executeS(
            'SELECT `id_product_attribute`
            FROM `' . _DB_PREFIX_ . 'product_attribute`
            WHERE `id_product` = ' . (int) $id_product
        );

        if (!$product_attributes) {
            return false;
        }

        $ids = [];

        foreach ($product_attributes as $product_attribute) {
            $ids[] = (int) $product_attribute['id_product_attribute'];
        }

        $result = \Db::getInstance()->executeS(
            '
            SELECT pai.`id_image`, pai.`id_product_attribute`, il.`legend`
            FROM `' . _DB_PREFIX_ . 'product_attribute_image` pai
            LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (il.`id_image` = pai.`id_image`)
            LEFT JOIN `' . _DB_PREFIX_ . 'image` i ON (i.`id_image` = pai.`id_image`)
            WHERE pai.`id_product_attribute` IN (' . implode(', ', $ids) . ') AND il.`id_lang` = ' . (int) $id_lang . ' ORDER by i.`position`'
        );

        if (!$result) {
            return false;
        }

        $images = [];

        foreach ($result as $row) {
            $images[$row['id_product_attribute']][] = $row;
        }

        return $images;
    }

    /**
     * Get product cover image.
     *
     * @param int $id_product Product identifier
     * @param \Context|null $context
     *
     * @return array Product cover image
     */
    public function getCover($id_product, \Context $context = null)
    {
        if (!$context) {
            $context = \Context::getContext();
        }
        $cache_id = 'Product::getCover_' . (int) $id_product . '-' . (int) $context->shop->id;
        if (!\Cache::isStored($cache_id)) {
            $sql = 'SELECT image_shop.`id_image`
                    FROM `' . _DB_PREFIX_ . 'image` i
                    ' . \Shop::addSqlAssociation('image', 'i') . '
                    WHERE i.`id_product` = ' . (int) $id_product . '
                    AND image_shop.`cover` = 1';
            $result = \Db::getInstance()->getRow($sql);
            \Cache::store($cache_id, $result);

            return $result;
        }

        return \Cache::retrieve($cache_id);
    }
}
