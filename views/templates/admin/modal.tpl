{*
* Scaleflex DAM Integration Modal
*}
<button type="button" class="btn btn-primary" style="display: none" id="scaleflex-dam-modal-btn"></button>

<div id="scaleflex-dam-modal">
    <div class="scaleflex-dam-modal-content">
        <div class="scaleflex-dam-modal-header">
            <h2>{l s='Scaleflex DAM Widget' mod='scaleflexdam'}</h2>
            <span class="scaleflex-dam-modal-close">&times;</span>
        </div>
        {* The container where the widget will be initialized *}
        <div id="scaleflex-dam-widget-container">
            <p style="color: #666;" id="scaleflex-dam-widget-loading">Loading Scaleflex DAM Widget...</p>
            <div id="scaleflex-dam-widget"></div>
        </div>
    </div>
</div>

<div id="scaleflex-widget-progress-panel"></div>

<script>
    const sfxDamActivation = true; // Placeholder for logic
    const container = '{$sfxDamToken|escape:'htmlall':'UTF-8'}';
    const templateId = '{$sfxDamSecTemplate|escape:'htmlall':'UTF-8'}';
    const uploadDir = '{$sfxDamUploadDir|escape:'htmlall':'UTF-8'}';
    const allowTransformations = '{$sfxDamAllowTransformations|escape:'htmlall':'UTF-8'}';
    const allowAiAssetSearch = '{$sfxDamAllowAiAssetSearch|escape:'htmlall':'UTF-8'}';
    const ajaxUrl = '{$ajaxUrl}'.replace(/&amp;/g, '&');
</script>