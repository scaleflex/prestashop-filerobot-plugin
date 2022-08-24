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
*  @copyright Scaleflex
*  @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
*}

<link rel="stylesheet" type="text/css"
      href="https://cdn.scaleflex.it/plugins/filerobot-widget/1.0.105/filerobot-widget.min.css?func=proxy"/>
<script type="text/javascript"
        src="https://cdn.scaleflex.it/plugins/filerobot-widget/1.0.105/filerobot-widget.min.js?func=proxy"></script>


<button type="button" class="btn btn-primary" style="display: none" id="filerobot-modal-btn" data-toggle="modal"
        data-target="#filerobotModal"></button>
<div class="modal fade" id="filerobotModal" tabindex="-1" role="dialog" aria-labelledby="filerobotLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filerobotLabel">Filerobot Media Asset Manager</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="filerobot-widget"></div>
            </div>
            <div class="modal-footer">
                <button type="button" id="filerobotModalClose" class="btn btn-secondary" data-dismiss="modal">Close
                </button>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        window.filerobot = undefined;

        $('.filerobotmanager').on('click', function () {
            $('#filerobot-modal-btn').trigger('click');
        });


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
            window.filerobot = filerobot;
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

                    const uploadUrl = jQuery('#product-images-dropzone').attr('url-upload');

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

                        if (window.fileRobotActiveEditor) {
                            window.fileRobotActiveEditor.execCommand('mceInsertContent', false, '<div>' +
                                '<img src="' + link + '" ' +
                                ' width="' + width + '"' +
                                ' height="' + height + '"' +
                                ' alt="' + selected.file.meta.title + '" /> ' +
                                '</div>');
                        } else {
                            const lastPreviewItem = $('.dz-preview ').last()

                            jQuery.post(uploadUrl, {
                                'type': 'filerobot',
                                'link': link
                            }).then(function (response) {
                                const html = '<div class="dz-preview dz-processing dz-image-preview dz-complete ui-sortable-handle"' +
                                    'url-delete="' + response.url_delete + '" ' +
                                    'url-update="' + response.url_update + '" ' +
                                    'data-id="' + response.id + '" ' +
                                    '>' +
                                    '<div class="dz-image bg" style="background-image: url(' + link + ')"></div>' +
                                    '<div class="dz-details">' +
                                    '<div class="dz-size"><span data-dz-size=""></span></div>' +
                                    '<div class="dz-filename"><span data-dz-name=""></span></div>' +
                                    ' </div>' +
                                    '</div>';
                                lastPreviewItem.after(html)
                            }).catch(function (error) {
                                //TODO:: Need todo something
                            });
                        }
                    })
                    jQuery('#filerobotModalClose').trigger('click');

                    if (!window.fileRobotActiveEditor) {
                        const dropZoneElem = $('#product-images-dropzone');
                        const expanderElem = $('#product-images-container .dropzone-expander');

                        const imageLength = dropZoneElem.find('.dz-preview:not(.filerobotmanager)').length;
                        const countRows = (imageLength + 1) / 5;
                        const height = (Math.ceil(countRows) * 171);
                        jQuery('#product-images-dropzone').css('height', height + 'px')
                    } else {
                        window.fileRobotActiveEditor = undefined;
                    }
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