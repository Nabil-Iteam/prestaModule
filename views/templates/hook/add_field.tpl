<div class ="row">
    <div class="col-md-12 left-column">
        <div class='summary-description-container'>
            <h2 class="form-control-label">{l s='on place price' d='Modules.Csoftaddfield.Admin'}</h2>
            <div id="cstextfield" class="mb-3">
                <div class="translations tabbable">
                    <div class="translationsFields tab-content">
                    {foreach from=$languages item=language}
                        <div class="translation-field translation-label-{$language.iso_code}  translation-label-{$language.id_lang} 
                        {if $default_language == $language.id_lang}show active{/if}" data-locale="{$language.iso_code}">
                            <input type="text" name="cstextfield" >
                        </div>
                    {/foreach}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>