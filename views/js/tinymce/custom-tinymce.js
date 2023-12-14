var customTinyMCE = {
    init: function () {
        window.defaultTinyMceConfig = {
            plugins: 'filerobot align colorpicker link image filemanager table media placeholder advlist code table autoresize',
            toolbar1: 'code,colorpicker,bold,italic,underline,strikethrough,blockquote,link,align,bullist,numlist,table,filerobot,media,formatselect',
        }
    },
};
$(function () {
    customTinyMCE.init();
});