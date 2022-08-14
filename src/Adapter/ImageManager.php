<?php

namespace Scaleflex\PrestashopFilerobot\Adapter;

use Image;
use ImageManager as LegacyImageManager;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Adapter\ImageManager as BaseImageManager;

class ImageManager extends BaseImageManager
{
    /**
     * @var LegacyContext
     */
    private $legacyContext;

    /**
     * @param LegacyContext $legacyContext
     */
    public function __construct(LegacyContext $legacyContext)
    {
        $this->legacyContext = $legacyContext;
    }

    /**
     * Old legacy way to generate a thumbnail.
     *
     * Use it upon a new Image management system is available.
     *
     * @param int $imageId
     * @param string $imageType
     * @param string $tableName
     * @param string $imageDir
     *
     * @return string The HTML < img > tag
     */
    public function getThumbnailForListing($imageId, $imageType = 'jpg', $tableName = 'product', $imageDir = _PS_PROD_IMG_DIR_)
    {
        $thumbPath = $this->getThumbnailTag($imageId, $imageType, $tableName, $imageDir);

        // because legacy uses relative path to reach a directory under root directory...
        $replacement = 'src="' . $this->legacyContext->getRootUrl();
        $thumbPath = preg_replace('/src="(\\.\\.\\/)+/', $replacement, $thumbPath);

        return $thumbPath;
    }

    /**
     * @param int $imageId
     *
     * @return string
     */
    public function getThumbnailPath($imageId)
    {
        $imageType = 'jpg';
        $tableName = 'product';
        $imageDir = _PS_PROD_IMG_DIR_;

        $imagePath = $this->getImagePath($imageId, $imageType, $tableName, $imageDir);
        $thumbnailCachedImageName = $this->makeCachedImageName($imageId, $imageType, $tableName);
        LegacyImageManager::thumbnail(
            $imagePath,
            $thumbnailCachedImageName,
            45,
            $imageType
        );

        return LegacyImageManager::getThumbnailPath($thumbnailCachedImageName, false);
    }

    /**
     * @param int $imageId
     * @param string $imageType
     * @param string $tableName
     * @param string $imageDir
     *
     * @return string
     */
    private function getThumbnailTag($imageId, $imageType, $tableName, $imageDir)
    {
        $imagePath = $this->getImagePath($imageId, $imageType, $tableName, $imageDir);

        if (str_contains($imagePath, 'filerobot')) {
            return '<img src="'.$imagePath.'&width=45&height=45" />';
        }

        return LegacyImageManager::thumbnail(
            $imagePath,
            $this->makeCachedImageName($imageId, $imageType, $tableName),
            45,
            $imageType
        );
    }

    /**
     * @param int $imageId
     * @param string $imageType
     * @param string $tableName
     * @param string $imageDir
     *
     * @return string
     */
    private function getImagePath($imageId, $imageType, $tableName, $imageDir)
    {
        if ($tableName == 'product') {
            $image = new \FilerobotImage($imageId);

            if ($image->url) {
                return $image->url;
            }

            return $imageDir . $image->getExistingImgPath() . '.' . $imageType;
        }

        return $imageDir . $imageId . '.' . $imageType;
    }

    /**
     * @param int $imageId
     * @param string $imageType
     * @param string $tableName
     *
     * @return string
     */
    private function makeCachedImageName($imageId, $imageType, $tableName)
    {
        return $tableName . '_mini_' . $imageId . '.' . $imageType;
    }
}