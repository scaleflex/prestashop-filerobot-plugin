<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Scaleflex\PrestashopFilerobot\Adapter;

use PrestaShop\PrestaShop\Adapter\Product\ProductDataProvider as BaseProductDataProvider;

/**
 * This class will provide data from DB / ORM about Product, for both Front and Admin interfaces.
 */
class ProductDataProvider extends BaseProductDataProvider
{
    /**
     * Get an image.
     *
     * @param int $id_image
     *
     * @return array()
     */
    public function getImage($id_image)
    {
        $imageData = new \FilerobotImage((int) $id_image);

        return [
            'id' => $imageData->id,
            'id_product' => $imageData->id_product,
            'position' => $imageData->position,
            'cover' => $imageData->cover ? true : false,
            'legend' => $imageData->legend,
            'format' => $imageData->image_format,
            'base_image_url' => _THEME_PROD_DIR_ . $imageData->getImgPath(),
            'url'   => $imageData->url
        ];
    }
}
