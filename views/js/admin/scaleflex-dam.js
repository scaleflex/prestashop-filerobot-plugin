/**
    * Scaleflex DAM - Admin Script
    */
jQuery(document).ready(function () {
    jQuery('#scaleflex-dam-modal-btn').on('click', function () {
        jQuery('#scaleflex-dam-modal').show();
    });
});

document.addEventListener('DOMContentLoaded', function () {
    console.log("Scaleflex DAM initialized");

    // Use event delegation because Vue components like Dropzone are rendered dynamically
    document.body.addEventListener('click', function (e) {
        // Check if the clicked element or its parent matches our upload buttons
        if (container && templateId) {
            let ScaleflexWidget = window.ScaleflexWidget;
            const target = e.target;
            const isUploadButton = target.closest('.dz-hidden-input, input[type="file"], .js-add-image, button[name="submitAddImage"], #file[type="file"], .openfilemanager, .dz-clickable');
            if (isUploadButton) {
                e.preventDefault();
                e.stopPropagation();

                const modal = document.getElementById('scaleflex-dam-modal');
                if (modal) {
                    modal.style.display = 'block';
                }
                console.log('Opening Scaleflex DAM Widget modal for product upload...');
                window.openScaleflexDamModal('product', null);
            }
        }
    }, true); // Use capture phase to intercept before Dropzone's internal listeners

    // Global function to open the modal from anywhere (TinyMCE, Product, etc)
    window.openScaleflexDamModal = function (mode = 'product', editor = null) {
        let ScaleflexWidget = window.ScaleflexWidget;
        var scaleflexWidget = ScaleflexWidget.Core({
            securityTemplateID: templateId,
            container: container,
        });

        // Plugins
        const Explorer = ScaleflexWidget.Explorer;
        const XHRUpload = ScaleflexWidget.XHRUpload;
        const ProgressPanel = ScaleflexWidget.ProgressPanel;

        let height = jQuery('body #scaleflex-dam-modal #scaleflex-dam-widget-container').height();

        let widgetConfig = {
            config: {
                rootFolderPath: uploadDir
            },
            target: '#scaleflex-dam-widget',
            inline: true,
            width: "100%",
            height: height,
            disableExportButton: false,
            disableDownloadButton: false,
            hideExportButtonIcon: true,
            preventExportDefaultBehavior: true,
            dismissUrlPathQueryUpdate: false,
            hideDownloadButtonIcon: true,
            preventDownloadDefaultBehavior: true,
            showProgressDetails: true,
            locale: {
                strings: {
                    mutualizedExportButtonLabel: "Insert Asset",
                    mutualizedDownloadButton: "Insert Asset"
                }
            },
            ExploreViewComponent: ScaleflexWidget.Explorer.ExploreViewComponent,
        };

        widgetConfig.hideDownloadVariationsOption = (parseInt(allowTransformations) === 1) ? false : true;
        widgetConfig.enableAIEmbed = (parseInt(allowAiAssetSearch) === 1) ? true : false;

        jQuery('#scaleflex-dam-widget-loading').hide();

        scaleflexWidget
            .use(Explorer, widgetConfig)
            .use(ProgressPanel, {
                target: '#scaleflex-widget-progress-panel',
            })
            .use(XHRUpload)
            .on('export', (files, popupExportSucessMsgFn, downloadFilesPackagedFn, downloadFileFn) => {
                console.dir(files);

                if (mode === 'tinymce' && editor) {
                    // TinyMCE Editor Insertion Logic
                    let htmlContent = '';
                    files.forEach(function (file) {
                        if (file.file.url) {
                            let DamUrl = file.file.url.cdn;
                            if (file.file.url.download !== undefined) {
                                DamUrl = file.file.url.download;
                            }

                            try {
                                const parsedUrl = new URL(DamUrl);
                                parsedUrl.searchParams.delete('vh');
                                DamUrl = parsedUrl.toString();
                            } catch (error) {
                                // Default to unparsed if there's an issue
                            }

                            let width = file.file.info && file.file.info.img_w ? file.file.info.img_w : '';
                            let height = file.file.info && file.file.info.img_h ? file.file.info.img_h : '';
                            let widthAttr = width ? ' width="' + width + '"' : '';
                            let heightAttr = height ? ' height="' + height + '"' : '';
                            htmlContent += '<img src="' + DamUrl + '" alt="' + (file.file.name || '') + '"' + widthAttr + heightAttr + ' />';
                        }
                    });

                    if (htmlContent) {
                        editor.insertContent(htmlContent);
                    }

                    const modal = document.getElementById('scaleflex-dam-modal');
                    if (modal) modal.style.display = 'none';

                    // Close widget explicitly if needed (optional)
                    scaleflexWidget.close();

                    return; // Stop product processing
                }

                // Original Product Image Upload Logic
                // Get id_product from URL params if in Products tab
                const urlParams = new URLSearchParams(window.location.search);
                let id_product = urlParams.get('id_product');

                // In PrestaShop 1.7.8+ with Symfony route, id_product is usually in the path
                if (!id_product) {
                    const match = window.location.pathname.match(/\/products\/(\d+)/);
                    if (match) {
                        id_product = match[1];
                    }
                }

                if (!id_product) {
                    // Fallback: Find input hidden in DOM
                    const inputId = document.querySelector('input[name="id_product"]');
                    if (inputId) id_product = inputId.value;
                }

                if (!id_product) {
                    alert('Cannot find product ID to attach images.');
                    return;
                }

                jQuery.ajax({
                    type: 'POST',
                    url: ajaxUrl,
                    data: {
                        id_product: id_product,
                        files: files
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            // Close modal
                            const modal = document.getElementById('scaleflex-dam-modal');
                            if (modal) modal.style.display = 'none';

                            // Trigger reload images (Trigger PrestaShop's internal Dropzone reset to fetch new images)
                            if (window.prestashop && window.prestashop.instance && window.prestashop.instance.eventEmitter) {
                                window.prestashop.instance.eventEmitter.emit('resetDropzone');
                            } else {
                                // Fallback reload page
                                window.location.reload();
                            }
                        } else {
                            alert('Error saving images: ' + (response.errors ? response.errors.join(', ') : response.message));
                        }
                    },
                    error: function (err) {
                        alert('AJAX Error: Could not save image to product.');
                        console.error(err);
                    }
                });
            });
    };

    const modalClose = document.querySelector('.scaleflex-dam-modal-close');
    if (modalClose) {
        modalClose.addEventListener('click', function () {
            const modal = document.getElementById('scaleflex-dam-modal');
            if (modal) {
                modal.style.display = 'none';
            }
        });
    }
});