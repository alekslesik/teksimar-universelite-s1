<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

/**
 * @var array $arResult
 * @var array $arVisual
 */

$sDescription = null;

if (!empty($arResult['DETAIL_TEXT']))
    $sDescription = $arResult['DETAIL_TEXT'];
else if ($arVisual['DESCRIPTION']['DETAIL']['FROM_PREVIEW'] && !empty($arResult['PREVIEW_TEXT']))
    $sDescription = $arResult['PREVIEW_TEXT'];
else
    return;

if (empty($arVisual['DESCRIPTION']['DETAIL']['NAME']))
    $arVisual['DESCRIPTION']['DETAIL']['NAME'] = Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_ADDITIONAL_DESCRIPTION');

?>
<div class="catalog-element-description catalog-element-additional-block">
    <div class="catalog-element-description-wrapper">
        <div class="catalog-element-additional-block-name">
            <?= $arVisual['DESCRIPTION']['DETAIL']['NAME'] ?>
        </div>
        <div class="catalog-element-additional-block-content-text">
            <?= $sDescription ?>
        </div>
    </div>
</div>
<?php unset($sDescription) ?>