<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

/**
 * @var string $componentName
 * @var string $templateName
 * @var string $siteTemplate
 * @var array $arCurrentValues
 */

$arTemplateParameters = [];
$arTemplateParameters['INPUT_ID'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_SEARCH_TITLE_POPUP_1_INPUT_ID'),
    'TYPE' => 'STRING'
];

$arTemplateParameters['TIPS_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_SEARCH_TITLE_POPUP_1_TIPS_USE'),
    'TYPE' => 'CHECKBOX'
];

$arTemplateParameters['TIPS_VIEW'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_SEARCH_TITLE_POPUP_1_TIPS_VIEW'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'list.1' => Loc::getMessage('C_SEARCH_TITLE_POPUP_1_TIPS_VIEW_LIST_1'),
        'list.2' => Loc::getMessage('C_SEARCH_TITLE_POPUP_1_TIPS_VIEW_LIST_2'),
        'list.3' => Loc::getMessage('C_SEARCH_TITLE_POPUP_1_TIPS_VIEW_LIST_3')
    ]
];

if (Loader::includeModule('catalog')) {
    include(__DIR__.'/parameters/catalog/base.php');
} else if (Loader::includeModule('intec.startshop')) {
    include(__DIR__.'/parameters/catalog/lite.php');
}

include(__DIR__.'/parameters/products.php');