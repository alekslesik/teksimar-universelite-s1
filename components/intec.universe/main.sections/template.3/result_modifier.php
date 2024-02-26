<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\template\Properties;
use Bitrix\Main\Localization\Loc;

/**
 * @var array $arParams
 * @var array $arResult
 */

$arParams = ArrayHelper::merge([
    'SETTINGS_USE' => 'N',
    'LAZYLOAD_USE' => 'N',
    'LINK_USE' => 'N',
    'WIDE' => 'N',
    'LINE_COUNT' => 4,
    'TEXT_SHOW' => 'N',
    'BUTTON_ALL_SHOW' => 'N',
    'BUTTON_ALL_TEXT' => null
], $arParams);

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arResult['VISUAL'] = ArrayHelper::merge($arResult['VISUAL'], [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') ? $arParams['LAZYLOAD_USE'] === 'Y' : false,
        'STUB' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] === 'Y' ? Properties::get('template-images-lazyload-stub') : null
    ],
    'LINK' => [
        'USE' => $arParams['LINK_USE'] === 'Y'
    ],
    'WIDE' => $arParams['WIDE'] === 'Y',
    'COLUMNS' => ArrayHelper::fromRange([3, 2, 4], $arParams['LINE_COUNT']),
    'TEXT' => [
        'SHOW' => $arParams['TEXT_SHOW'] === 'Y'
    ],
    'BUTTON_ALL' => [
        'SHOW' => $arParams['BUTTON_ALL_SHOW'] === 'Y',
        'TEXT' => $arParams['BUTTON_ALL_TEXT'],
        'LINK' => StringHelper::replaceMacros(ArrayHelper::getValue($arParams, 'LIST_PAGE_URL'), [
            'SITE_DIR' => SITE_DIR
        ])
    ]
]);

$arVisual = &$arResult['VISUAL'];

if ($arVisual['BUTTON_ALL']['SHOW'] && empty($arVisual['BUTTON_ALL']['LINK'])) {
    $arIblock = Arrays::fromDBResult(CIBlock::GetByID($arParams['IBLOCK_ID']))->getFirst();
    $arMacros = [
        'SITE_DIR' => SITE_DIR,
        'SERVER_NAME' => $_SERVER['SERVER_NAME'],
        'IBLOCK_TYPE_ID' => $arParams['IBLOCK_TYPE'],
        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
        'IBLOCK_CODE' => $arIblock['CODE'],
        'IBLOCK_EXTERNAL_ID' => !empty($arIblock['EXTERNAL_ID']) ? $arIblock['EXTERNAL_ID'] : $arIblock['XML_ID']
    ];
    $arSection = ArrayHelper::getFirstValue($arResult['SECTIONS']);
    $arVisual['BUTTON_ALL']['LINK'] = StringHelper::replaceMacros($arSection['LIST_PAGE_URL'], $arMacros);

    unset($arIblock, $arMacros, $arSection);
}

$arKeys = [2, 0, 1, 1, 1, 2];

foreach ($arResult['SECTIONS'] as &$arItem) {
    if (isset($arItem['ELEMENT_CNT'])) {
        $fMod = $arItem['ELEMENT_CNT'] % 100;
        $iSuffixKey = $fMod > 4 && $fMod < 20 ? 2 : $arKeys[min($fMod % 10, 5)];
        $arItem['ELEMENT_CNT_DISPLAY'] = $arItem['ELEMENT_CNT'] . ' ' . Loc::getMessage('C_MAIN_SECTIONS_TEMPLATE_3_PRODUCT_' . $iSuffixKey);
    }
}

unset($arKeys, $arVisual);