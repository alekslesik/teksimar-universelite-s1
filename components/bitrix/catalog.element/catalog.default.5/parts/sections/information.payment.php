<?php

/**
 * @var array $arVisual
 * @var CMain $APPLICATION
 * @var CBitrixComponent $component
 */

?>
<div class="catalog-element-sections-content-text">
    <?php $APPLICATION->IncludeComponent(
        'bitrix:main.include',
        '', [
            'AREA_FILE_SHOW' => 'file',
            'PATH' => $arVisual['INFORMATION']['PAYMENT']['PATH'],
            'EDIT_TEMPLATE' => ''
        ],
        $component
    ) ?>
</div>
