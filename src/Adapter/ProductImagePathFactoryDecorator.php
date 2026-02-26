<?php

namespace ScaleflexDam\Adapter;

use PrestaShop\PrestaShop\Adapter\Product\Image\ProductImagePathFactory;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\ValueObject\ImageId;
use Db;
use Context;
use ImageType;

class ProductImagePathFactoryDecorator extends ProductImagePathFactory
{
    public function __construct(
        string $pathToBaseDir,
        string $temporaryImgDir,
        string $contextLangIsoCode
    ) {
        parent::__construct($pathToBaseDir, $temporaryImgDir, $contextLangIsoCode);
    }

    private function getDamUrlOrProxy(int $id_image, string $type = null)
    {
        $url = Db::getInstance()->getValue(
            'SELECT `url` FROM `' . _DB_PREFIX_ . 'image` WHERE `id_image` = ' . (int)$id_image
        );

        if ($url && !empty($url)) {
            // Dropzone limits images loaded from different origins.
            // By returning a local proxy URL to the Dropzone, we bypass the aggressive origin rewrite mechanism of Vue.
            if (defined('_PS_ADMIN_DIR_') || (isset(Context::getContext()->controller) && Context::getContext()->controller->controller_type == 'admin')) {
                $context = Context::getContext();
                return $context->link->getModuleLink('scaleflexdam', 'proxy', ['id_image' => (int)$id_image]);
            }

            // Append resize parameters for Front-Office if $type is provided
            if ($type) {
                $imageType = ImageType::getByNameNType($type, 'products');
                if ($imageType && isset($imageType['width']) && isset($imageType['height'])) {
                    $separator = (strpos((string)$url, '?') !== false) ? '&' : '?';
                    $url .= $separator . 'w=' . (int)$imageType['width'] . '&h=' . (int)$imageType['height'] . '&func=fit';
                }
            }

            return $url;
        }

        return null;
    }

    public function getPath(ImageId $imageId, string $extension = self::DEFAULT_IMAGE_FORMAT): string
    {
        $url = $this->getDamUrlOrProxy($imageId->getValue());
        if ($url) {
            return $url;
        }

        return parent::getPath($imageId, $extension);
    }

    public function getPathByType(ImageId $imageId, string $type, string $extension = self::DEFAULT_IMAGE_FORMAT): string
    {
        $url = $this->getDamUrlOrProxy($imageId->getValue(), $type);
        if ($url) {
            return $url;
        }

        return parent::getPathByType($imageId, $type, $extension);
    }
}
