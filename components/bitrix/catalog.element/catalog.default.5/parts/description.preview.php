<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

/**
 * @var array $arResult
 * @var array $arVisual
 */

$sDescription = null;

if (!empty($arResult['PREVIEW_TEXT']))
    $sDescription = $arResult['PREVIEW_TEXT'];

if (empty($sDescription) || (empty($arResult['DETAIL_TEXT']) && $arVisual['DESCRIPTION']['DETAIL']['FROM_PREVIEW']))
    return;
?>
<div class="catalog-element-description-preview-block-container catalog-element-main-block">
    <div class="catalog-element-description-preview">
        <?= $sDescription ?>
    </div>
</div>
<?php unset($sDescription) ?>