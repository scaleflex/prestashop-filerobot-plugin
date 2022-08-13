<?php

namespace Scaleflex\PrestashopFilerobot\Controller\Admin;

use FilerobotImage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;
use PrestaShopBundle\Controller\Admin\ProductImageController as BaseProductImageController;

class ProductImageController extends BaseProductImageController
{
    const TYPE_FILEROBOT = 'filerobot';

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

        if ($idProduct == 0 || !$request->isXmlHttpRequest()) {
            return $response;
        }

       if ($request->get('type') && $request->get('type') === self::TYPE_FILEROBOT) {
           // Save product image to database
           $image = new FilerobotImage();
           $image->id_product = (int) ($idProduct);
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
               ->getForm();

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