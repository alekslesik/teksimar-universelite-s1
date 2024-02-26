<?php if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\helpers\ArrayHelper;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponent $component
 * @var array $arForms
 */

$arGallery = ArrayHelper::getValue($arResult, 'GALLERY');

if (!empty($arResult['PREVIEW_PICTURE'])) {
    $arGallery = ArrayHelper::merge([$arResult['PREVIEW_PICTURE']], $arGallery);
}

if (!empty($arResult['DETAIL_PICTURE'])) {
    $arGallery = ArrayHelper::merge([$arResult['DETAIL_PICTURE']], $arGallery);
}

?>

<div class="intec-content">
    <div class="intec-content-wrapper">
        <div class="news-detail-content-header intec-grid intec-grid-wrap">
            <?php include(__DIR__.'/header/previews.php') ?>
            <?php include(__DIR__.'/header/gallery.php') ?>
            <div class="news-detail-content-header-info intec-grid-item">
                <?php include(__DIR__.'/header/properties.php') ?>
                <div class="news-detail-content-header-info-description intec-ui-markup-text">
                    <?php if (!empty($arResult['PREVIEW_TEXT'])) { ?>
                        <?= $arResult['PREVIEW_TEXT'] ?>
                    <?php } else if (!empty($arResult['DETAIL_TEXT'])) { ?>
                        <?= $arResult['DETAIL_TEXT'] ?>
                    <?php } ?>
                </div>
                <?php include(__DIR__.'/header/buttons.php') ?>
            </div>
        </div>
    </div>
</div>
