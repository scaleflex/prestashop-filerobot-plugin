<?php
/**
* Scaleflex DAM - Link Override
*/

class Link extends LinkCore
{
    /**
     * Override to return the Scaleflex DAM URL if it exists.
     * 
     * @param string $name
     * @param string|int $idImage numeric ID of product image or a name of default image like "fr-default"
     * @param string|null $type Image thumbnail name (small_default, medium_default, large_default, etc.)
     * @param string $extension What image extension should the link point to
     *
     * @return string
     */
    public function getImageLink($name, $idImage, $type = null, string $extension = 'jpg')
    {
        // Check if we have an image ID instead of default identifier
        if (!strpos((string)$idImage, 'default')) {
            // Support legacy format productId-imageId
            $realIdImage = $idImage;
            if (strpos((string)$idImage, '-')) {
                $pieces = explode('-', (string)$idImage);
                $realIdImage = $pieces[1];
            }
            
            // Fast query to check if this image has a custom URL set
            $url = Db::getInstance()->getValue(
                'SELECT `url` FROM `' . _DB_PREFIX_ . 'image` WHERE `id_image` = ' . (int)$realIdImage
            );

            if ($url && !empty($url)) {
                // If in BackOffice, return a local proxy URL to avoid Dropzone's origin rewrite issue
                if (defined('_PS_ADMIN_DIR_') || (isset(Context::getContext()->controller) && Context::getContext()->controller->controller_type == 'admin')) {
                    $context = Context::getContext();
                    return $context->link->getModuleLink('scaleflexdam', 'proxy', ['id_image' => (int)$realIdImage]);
                }

                // Return the exact DAM URL for Front-Office
                // Note: you can manipulate $url to append resize params from DAM based on $type here
                if ($type) {
                    $imageType = ImageType::getByNameNType($type, 'products');
                    if ($imageType && isset($imageType['width']) && isset($imageType['height'])) {
                        $separator = (strpos((string)$url, '?') !== false) ? '&' : '?';
                        $url .= $separator . 'w=' . (int)$imageType['width'] . '&h=' . (int)$imageType['height'] . '&func=fit';
                    }
                }
                
                return $url;
            }
        }

        return parent::getImageLink($name, $idImage, $type, $extension);
    }
}
