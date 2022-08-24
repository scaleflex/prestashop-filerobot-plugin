/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 * You must not modify, adapt or create derivative works of this source code
 *  @author    Scaleflex
 *  @copyright Since 2022 Scaleflex
 *  @license   LICENSE.txt
 */

jQuery(document).ready(function () {
    if (tinymce && window.filerobot) {
        tinymce.PluginManager.add('filerobot', function (editor, url) {
            editor.addButton('filerobot', {
                icon: 'image',
                onclick: function () {
                    window.fileRobotActiveEditor = editor;
                    jQuery("#filerobot-modal-btn").trigger('click')
                }
            });

            editor.on('ObjectResizeStart', function (e) {
                window.activeObject = e;
            });

            editor.on('ObjectResized', function (e) {
                const object = jQuery(e.target);

                console.log(object.prop('nodeName'))

                if (object.prop('nodeName') === 'IMG') {
                    // Get OLD Value
                    const oldObject = window.activeObject;
                    const oldWidth = oldObject.width;
                    const oldHeight = oldObject.height;

                    const newWidth = e.width;
                    const newHeight = e.height;
                    const src = object.attr('src');

                    // Update src URL
                    let newSrc = src.replace(oldWidth, newWidth);
                    newSrc = newSrc.replace(oldHeight, newHeight);

                    // Update image url after resized
                    let content = editor.getContent();
                    content = content.replaceAll('&amp;', '&');
                    content = content.replace(src, newSrc);
                    editor.setContent(content);
                    window.activeObject = undefined;
                }
            });

            return {
                getMetadata: function () {
                    return {
                        name: "Filerobot TinyMCE Plugin",
                        url: "https://scaleflex.com"
                    };
                }
            };
        });

        window.defaultTinyMceConfig = {
            plugins: 'filerobot align colorpicker link image filemanager table media placeholder advlist code table autoresize',
            toolbar1: 'code,colorpicker,bold,italic,underline,strikethrough,blockquote,link,align,bullist,numlist,table,filerobot,media,formatselect',
        }
    }
});