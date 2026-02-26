window.defaultTinyMceConfig = {
    plugins: 'align colorpicker link image filemanager table media placeholder lists advlist code table autoresize hr scaleflexdam',
    toolbar1: 'code,colorpicker,bold,italic,underline,strikethrough,blockquote,link,align,bullist,numlist,table,image,media,formatselect,hr,scaleflexdam',
};

function registerScaleflexPlugin() {
    if (typeof tinymce === 'undefined') {
        setTimeout(registerScaleflexPlugin, 100);
        return;
    }

    tinymce.PluginManager.add('scaleflexdam', function (editor, url) {
        editor.addButton('scaleflexdam', {
            icon: 'image',
            tooltip: 'Insert Scaleflex DAM Asset',
            onclick: function () {
                // Call global function defined in admin/scaleflex-dam.js
                if (typeof window.openScaleflexDamModal === 'function') {
                    const modal = document.getElementById('scaleflex-dam-modal');
                    if (modal) {
                        modal.style.display = 'block';
                    }

                    window.openScaleflexDamModal('tinymce', editor);
                } else {
                    console.error('Scaleflex DAM modal function is not available.');
                }
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
                let newSrc = src;
                try {
                    let urlObj = new URL(src);
                    urlObj.searchParams.set('w', newWidth);
                    urlObj.searchParams.set('h', newHeight);
                    newSrc = urlObj.toString();
                } catch (error) {
                    // Fallback in case of invalid URL
                    newSrc = src.replace(oldWidth, newWidth);
                    newSrc = newSrc.replace(oldHeight, newHeight);
                }

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
                    name: "Scaleflex DAM",
                    url: "https://scaleflex.com"
                };
            }
        };
    });
}

registerScaleflexPlugin();
