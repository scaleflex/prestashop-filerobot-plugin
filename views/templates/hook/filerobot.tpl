{**
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
*  @author 2022 Scaleflex
*  @author Tung Dang <tung.dang@scaleflex.com>
*  @copyright Scaleflex
*  @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
*}
<link rel="stylesheet" type="text/css"
      href="https://cdn.scaleflex.it/plugins/filerobot-widget/1.0.105/filerobot-widget.min.css?func=proxy"/>
<script type="text/javascript"
        src="https://cdn.scaleflex.it/plugins/filerobot-widget/1.0.105/filerobot-widget.min.js?func=proxy"></script>

<button style="display: none" type="button" id="filerobot-modal-btn" class="btn  btn-primary"
        data-toggle="modal" data-target="#filerobotModal" >
</button>
<div class="modal fade" id="filerobotModal" tabindex="-1"
     role="dialog" aria-labelledby="Filerobot Modal" aria-hidden="true">
    <div style="margin: 20px auto" class="modal-lg" role="document">
        <div class="modal-content">
            <div id="modal-content" class="modal-body">
                <div id="filerobot-widget"></div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {

        // $('.filerobotmanager').on('click', function () {
        //     $('#filerobot-modal-btn').trigger('click');
        // });


        const container = '{$frToken|escape:'htmlall':'UTF-8'}';
        const templateId = '{$frSecTemplate|escape:'htmlall':'UTF-8'}';
        const uploadDir = '{$frUploadDir|escape:'htmlall':'UTF-8'}';

        const Filerobot = window.Filerobot;

        if (container && templateId) {
            const filerobot = Filerobot.Core({
                securityTemplateID: templateId,
                container: container
            });

            const Explorer = Filerobot.Explorer;
            const XHRUpload = Filerobot.XHRUpload;
            const ImageEditor = Filerobot.ImageEditor;
            const Webcam = Filerobot.Webcam;

            filerobot
                .use(Explorer, {
                    config: {
                        rootFolderPath: uploadDir ?? '/'
                    },
                    target: '#filerobot-widget',
                    inline: true,
                    width: '100%',
                    height: 530,
                    showDetailsView: true,
                    locale: {
                        strings: {
                            export: 'Insert'
                        }
                    },
                })
                .use(XHRUpload)
                .use(ImageEditor, {
                    cloudimageToken: templateId
                })
                .use(Webcam)
                .on('export', function (files, popupExportSuccessMsgFn, downloadFilesPackagedFn, downloadFileFn) {
                    files.forEach((selected, key) => {
                        let link = selected.link;

                        let url = new URL(link);
                        let width = selected.file.info.img_w;
                        let height = selected.file.info.img_h;

                        if (!link.includes('width=')) {
                            link += ('&width=' + width);
                        } else {
                            width = url.searchParams.get("width");
                        }

                        if (!link.includes('height=')) {
                            link += ('&height=' + height);
                        } else {
                            height = url.searchParams.get("height");
                        }

                        window.fileRobotActiveEditor.execCommand('mceInsertContent', false, '<div>' +
                            '<img src="' + link + '" ' +
                            ' width="' + width + '"' +
                            ' height="' + height + '"' +
                            ' alt="' + selected.file.meta.title + '" /> ' +
                            '</div>');
                    })
                    window.fileRobotActiveEditor = undefined
                    jQuery('#modal-content').modal('closeModal')
                    files = []
                    return false
                });
        }
    })
</script>

<style>
    .product-page #product-images-dropzone.dropzone .dz-preview.filerobotmanager div {
        position: relative;
        top: 50%;
        display: inline-block;
        width: 50px;
        height: 50px;
        border: 5px solid #bbcdd2;
        border-radius: 100%;
        -webkit-transform: translateY(-50%);
        transform: translateY(-50%);
    }

    .product-page #product-images-dropzone.dropzone .dz-preview.filerobotmanager {
        color: #bbcdd2;
        text-align: center;
    }

    .product-page #product-images-dropzone.dropzone .dz-preview.filerobotmanager div span {
        display: inline-block;
        height: 50px;
        font-size: 30px;
        font-weight: 600;
        line-height: 40px;
        text-align: center;
    }

</style>