<?php
/**
* 2007-2026 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2026 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminScaleflexDamController extends ModuleAdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->ajax = true;
    }

    public function displayAjaxAddImage()
    {
        $id_product = (int) Tools::getValue('id_product');
        $files = Tools::getValue('files'); // Array of file data from Scaleflex

        if (!$id_product) {
            die(json_encode([
                'success' => false,
                'message' => $this->l('Product ID is missing.')
            ]));
        }

        if (empty($files) || !is_array($files)) {
            die(json_encode([
                'success' => false,
                'message' => $this->l('No files provided.')
            ]));
        }

        $product = new Product($id_product);
        if (!Validate::isLoadedObject($product)) {
            die(json_encode([
                'success' => false,
                'message' => $this->l('Product not found.')
            ]));
        }

        $addedImages = [];
        $errors = [];

        foreach ($files as $fileData) {
            try {
                // Determine cover status. If product has no cover, first image becomes cover.
                $is_cover = !Image::getCover($id_product);
                
                // Extract relevant data from the DAM JSON
                if (isset($fileData['url']['download'])) {
                    $damUrl = $fileData['url']['download'];
                } else {
                    $damUrl = isset($fileData['url']['cdn']) ? $fileData['url']['cdn'] : (isset($fileData['link']) ? $fileData['link'] : '');
                }

                // Remove parameters vh= and func=
                $parsedUrl = parse_url($damUrl);
                if (isset($parsedUrl['query'])) {
                    parse_str($parsedUrl['query'], $queryParams);
                    unset($queryParams['vh'], $queryParams['func']);
                    
                    $newQuery = http_build_query($queryParams);
                    $scheme = isset($parsedUrl['scheme']) ? $parsedUrl['scheme'] . '://' : '';
                    $host = isset($parsedUrl['host']) ? $parsedUrl['host'] : '';
                    $port = isset($parsedUrl['port']) ? ':' . $parsedUrl['port'] : '';
                    $path = isset($parsedUrl['path']) ? $parsedUrl['path'] : '';
                    
                    $damUrl = $scheme . $host . $port . $path . (!empty($newQuery) ? '?' . $newQuery : '');
                }

                if (empty($damUrl)) {
                    $errors[] = $this->l('A file is missing a valid URL.');
                    continue;
                }

                $image = new Image();
                $image->id_product = $id_product;
                $image->position = Image::getHighestPosition($id_product) + 1;
                $image->cover = $is_cover;
                
                // Add the custom URL to our new column
                $image->url = pSQL($damUrl);

                // Set legend if available in meta
                $legend = [];
                $languages = Language::getLanguages(false);
                $titleMeta = isset($fileData['file']['meta']['title']) ? $fileData['file']['meta']['title'] : [];
                $altText = !empty($titleMeta) ? reset($titleMeta) : (isset($fileData['file']['name']) ? $fileData['file']['name'] : '');
                
                foreach ($languages as $lang) {
                    $legend[$lang['id_lang']] = $altText;
                }
                $image->legend = $legend;

                if ($image->add()) {
                    // Associate image with shops
                    $image->associateTo($this->context->shop->id);

                    // Since we're not uploading a physical file, we don't need to do standard PrestaShop image processing.
                    // We just return the image data for the frontend to update the view.
                    
                    $addedImages[] = [
                        'id_image' => $image->id,
                        'position' => $image->position,
                        'cover' => $image->cover,
                        'url' => $image->url, // Expose the DAM URL
                    ];
                } else {
                    $errors[] = $this->l('Failed to save image record.');
                }
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        die(json_encode([
            'success' => empty($errors),
            'images' => $addedImages,
            'errors' => $errors
        ]));
    }
}
