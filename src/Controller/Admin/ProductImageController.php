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

namespace Scaleflex\PrestashopFilerobot\Controller\Admin;

use PrestaShopBundle\Controller\Admin\ProductImageController as BaseProductImageController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class ProductImageController extends BaseProductImageController
{
    public const TYPE_FILEROBOT = 'filerobot';

    /**
     * Manage upload for product image.
     *
     * @param int $idProduct
     * @param Request $request
     *
     * @return string
     */
    public function uploadImageAction($idProduct, Request $request)
    {
        $response = new JsonResponse();
        $return_data = [];

        if (0 == $idProduct || !$request->isXmlHttpRequest()) {
            return $response;
        }

        if ($request->get('type') && self::TYPE_FILEROBOT === $request->get('type')) {
            // Save product image to database
            $image = new \FilerobotImage();
            $image->id_product = (int) $idProduct;
            $image->position = \ImageCore::getHighestPosition($idProduct) + 1;

            if (!\ImageCore::getCover($image->id_product)) {
                $image->cover = 1;
            } else {
                $image->cover = 0;
            }

            $image->url = $request->get('link');
            $image->save();

            $return_data['id'] = $image->id;
            $return_data['cover'] = $image->cover;

            $return_data = array_merge($return_data, [
                'url_update' => $this->generateUrl('admin_product_image_form', ['idImage' => $return_data['id']]),
                'url_delete' => $this->generateUrl('admin_product_image_delete', ['idImage' => $return_data['id']]),
            ]);

            // Return type as Dropzone
            return $response->setData($return_data);
        } else {
            $adminProductWrapper = $this->get('prestashop.adapter.admin.wrapper.product');
            $form = $this->createFormBuilder(null, ['csrf_protection' => false])
                ->add('file', 'Symfony\Component\Form\Extension\Core\Type\FileType', [
                    'error_bubbling' => true,
                    'constraints' => [
                        new Assert\NotNull(['message' => $this->trans('Please select a file', 'Admin.Catalog.Feature')]),
                        new Assert\Image(['maxSize' => $this->configuration->get('PS_ATTACHMENT_MAXIMUM_SIZE') . 'M']),
                    ],
                ])
                ->getForm()
            ;

            $form->handleRequest($request);

            if ($request->isMethod('POST')) {
                if ($form->isValid()) {
                    $return_data = $adminProductWrapper->getInstance()->ajaxProcessaddProductImage($idProduct, 'form', false)[0];
                    $return_data = array_merge($return_data, [
                        'url_update' => $this->generateUrl('admin_product_image_form', ['idImage' => $return_data['id']]),
                        'url_delete' => $this->generateUrl('admin_product_image_delete', ['idImage' => $return_data['id']]),
                    ]);
                } else {
                    $error_msg = [];
                    foreach ($form->getErrors() as $error) {
                        $error_msg[] = $error->getMessage();
                    }
                    $return_data = ['message' => implode(' ', $error_msg)];
                    $response->setStatusCode(400);
                }
            }

            return $response->setData($return_data);
        }
    }
}
