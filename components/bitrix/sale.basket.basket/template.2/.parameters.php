<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Catalog\CatalogIblockTable;
use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Web\Json;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;

CBitrixComponent::includeComponentClass($componentName);

if (!Loader::includeModule('intec.core'))
    return;

if (!Loader::includeModule('iblock'))
    return;

$bBase = false;

if (Loader::includeModule('catalog') && Loader::includeModule('sale')) {
    $bBase = true;
}

$bMeasures = false;

if ($bBase && Loader::includeModule('intec.measures'))
    $bMeasures = true;

$arTemplateParameters['SETTINGS_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_BASKET_TEMPLATE_DEFAULT_2_SETTINGS_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];
$arTemplateParameters['COLUMNS_LIST_MOBILE'] = [
	'PARENT' => 'VISUAL',
	'NAME' => Loc::getMessage('C_BASKET_TEMPLATE_DEFAULT_2_COLUMNS_LIST_MOBILE'),
	'TYPE' => 'LIST',
	'COLS' => 25,
	'SIZE' => 7,
	'MULTIPLE' => 'Y',
];
$arTemplateParameters['DEFERRED_REFRESH'] = [
	'PARENT' => 'BASE',
	'NAME' => Loc::getMessage('C_BASKET_TEMPLATE_DEFAULT_2_DEFERRED_REFRESH'),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'N'
];
$arTemplateParameters['USE_DYNAMIC_SCROLL'] = [
	'PARENT' => 'BASE',
	'NAME' => Loc::getMessage('C_BASKET_TEMPLATE_DEFAULT_2_USE_DYNAMIC_SCROLL'),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'Y'
];
$arTemplateParameters['SHOW_FILTER'] = [
	'PARENT' => 'BASE',
	'NAME' => Loc::getMessage('C_BASKET_TEMPLATE_DEFAULT_2_SHOW_FILTER'),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'Y'
];
$arTemplateParameters['SHOW_RESTORE'] = [
	'PARENT' => 'BASE',
	'NAME' => Loc::getMessage('C_BASKET_TEMPLATE_DEFAULT_2_SHOW_RESTORE'),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'Y'
];
$arTemplateParameters['CONFIRM_REMOVE_PRODUCT_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_BASKET_TEMPLATE_DEFAULT_2_CONFIRM_REMOVE_PRODUCT_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];
$arTemplateParameters['EMPTY_BASKET_HINT_PATH'] = [
	'PARENT' => 'ADDITIONAL_SETTINGS',
	'NAME' => Loc::getMessage('C_BASKET_TEMPLATE_DEFAULT_2_EMPTY_BASKET_HINT_PATH'),
	'TYPE' => 'STRING',
	'DEFAULT' => '/'
];
$arTemplateParameters['TOTAL_BLOCK_FIXED_MODE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_BASKET_TEMPLATE_DEFAULT_2_TOTAL_BLOCK_FIXED_MODE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];
$arTemplateParameters['TOTAL_BLOCK_DISPLAY'] = [
	'PARENT' => 'VISUAL',
	'NAME' => Loc::getMessage('C_BASKET_TEMPLATE_DEFAULT_2_TOTAL_BLOCK_DISPLAY'),
	'TYPE' => 'LIST',
	'VALUES' => [
		'top' => Loc::getMessage('C_BASKET_TEMPLATE_DEFAULT_2_TOTAL_BLOCK_DISPLAY_TOP'),
		'bottom' => Loc::getMessage('C_BASKET_TEMPLATE_DEFAULT_2_TOTAL_BLOCK_DISPLAY_BOTTOM')
	],
	'DEFAULT' => [
	    'top'
    ],
	'MULTIPLE' => 'Y'
];
$arTemplateParameters['PRODUCT_BLOCKS_ORDER'] = [
	'PARENT' => 'VISUAL',
	'NAME' => Loc::getMessage('C_BASKET_TEMPLATE_DEFAULT_2_PRODUCT_BLOCKS_ORDER'),
	'TYPE' => 'CUSTOM',
	'JS_FILE' => CBitrixBasketComponent::getSettingsScript($componentPath, 'dragdrop_order'),
	'JS_EVENT' => 'initDraggableOrderControl',
	'JS_DATA' => Json::encode([
		'props' => Loc::getMessage('C_BASKET_TEMPLATE_DEFAULT_2_PRODUCT_BLOCKS_ORDER_PROPERTIES'),
		'sku' => Loc::getMessage('C_BASKET_TEMPLATE_DEFAULT_2_PRODUCT_BLOCKS_ORDER_SKU'),
		'columns' => Loc::getMessage('C_BASKET_TEMPLATE_DEFAULT_2_PRODUCT_BLOCKS_ORDER_COLUMNS')
	]),
	'DEFAULT' => 'props,sku,columns'
];
$arTemplateParameters['TOTAL_WEIGHT_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_BASKET_TEMPLATE_DEFAULT_2_TOTAL_WEIGHT_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];
$arTemplateParameters['TOTAL_VOLUME_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_BASKET_TEMPLATE_DEFAULT_2_TOTAL_VOLUME_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];
$arOfferProperties = [];

if (Loader::includeModule('catalog')) {
    $arOfferData = [];
    $arOfferIBlocks = [];

    /** Получение всех свойств из всех каталогов */
	$rsProperties = CatalogIblockTable::getList([
		'select' => [
		    'IBLOCK_ID',
            'PRODUCT_IBLOCK_ID',
            'SKU_PROPERTY_ID'
        ],
		'filter' => [
		    '!=PRODUCT_IBLOCK_ID' => 0
        ]
	]);

	while ($arProperty = $rsProperties->fetch()) {
        $arOfferIBlocks[] = $arProperty['IBLOCK_ID'];
        $arOfferData[$arProperty['IBLOCK_ID']] = $arProperty;
	}

	unset($rsProperties, $arProperty);

	foreach ($arOfferIBlocks as $sIBlock) {
		$rsProperties = CIBlockProperty::GetList([
				'SORT' => 'ASC',
				'NAME' => 'ASC'
			], [
				'IBLOCK_ID' => $sIBlock,
				'ACTIVE' => 'Y',
				'CHECK_PERMISSIONS' => 'N',
			]
		);

		while ($arProperty = $rsProperties->GetNext()) {
			if ($arProperty['ID'] === $arOfferData[$sIBlock]['SKU_PROPERTY_ID'])
				continue;

			if ($arProperty['XML_ID'] === 'CML2_LINK')
				continue;

			if ($arProperty['PROPERTY_TYPE'] !== 'F')
			    $arOfferProperties[$arProperty['CODE']] = '['.$arProperty['CODE'].'] '.$arProperty['NAME'];
		}

		unset($rsProperties, $arProperty);
	}

	unset($arOfferData, $arOfferIBlocks, $sIBlock);
}

if (!empty($arOfferProperties) && Type::isArray($arOfferProperties)) {
    $arTemplateParameters['OFFERS_PROPS'] = [
        'PARENT' => 'OFFERS_PROPS',
        'NAME' => Loc::getMessage('C_BASKET_TEMPLATE_DEFAULT_2_OFFERS_PROPS'),
        'TYPE' => 'LIST',
        'MULTIPLE' => 'Y',
        'SIZE' => '7',
        'ADDITIONAL_VALUES' => 'N',
        'REFRESH' => 'N',
        'DEFAULT' => '-',
        'VALUES' => $arOfferProperties
    ];
}

$arTemplateParameters['USE_ENHANCED_ECOMMERCE'] = [
	'PARENT' => 'ANALYTICS_SETTINGS',
	'NAME' => Loc::getMessage('C_BASKET_TEMPLATE_DEFAULT_2_USE_ENHANCED_ECOMMERCE'),
	'TYPE' => 'CHECKBOX',
	'REFRESH' => 'Y',
	'DEFAULT' => 'N'
];

if ($arCurrentValues['USE_ENHANCED_ECOMMERCE'] === 'Y') {
    $arTemplateParameters['DATA_LAYER_NAME'] = [
        'PARENT' => 'ANALYTICS_SETTINGS',
        'NAME' => Loc::getMessage('C_BASKET_TEMPLATE_DEFAULT_2_DATA_LAYER_NAME'),
        'TYPE' => 'STRING',
        'DEFAULT' => 'dataLayer'
    ];

    $arPropertiesList = [];

	if (Loader::includeModule('catalog')) {
		$arIBlockID = [];
		$arIBlockName = [];

		$rsCatalog = CatalogIblockTable::getList([
			'select' => [
			    'IBLOCK_ID',
                'NAME' => 'IBLOCK.NAME'
            ],
			'order' => [
			    'IBLOCK_ID' => 'ASC'
            ]
		]);

		while ($arCatalog = $rsCatalog->fetch()) {
            $arCatalog['IBLOCK_ID'] = (int)$arCatalog['IBLOCK_ID'];
			$arIBlockID[] = $arCatalog['IBLOCK_ID'];
			$arIBlockName[$arCatalog['IBLOCK_ID']] = $arCatalog['NAME'];
		}

		unset($rsCatalog, $arCatalog);

		if (!empty($arIBlockID)) {
			$arProperties = [];

			$rsProperties = PropertyTable::getList([
				'select' => [
				    'ID',
                    'CODE',
                    'NAME',
                    'IBLOCK_ID'
                ],
				'filter' => [
				    '@IBLOCK_ID' => $arIBlockID,
                    '=ACTIVE' => 'Y',
                    '!=XML_ID' => CIBlockPropertyTools::XML_SKU_LINK
                ],
				'order' => [
				    'IBLOCK_ID' => 'ASC',
                    'SORT' => 'ASC',
                    'ID' => 'ASC'
                ]
			]);

			while ($arProperty = $rsProperties->fetch()) {
                $arProperty['ID'] = (int)$arProperty['ID'];
                $arProperty['IBLOCK_ID'] = (int)$arProperty['IBLOCK_ID'];
                $arProperty['CODE'] = (string)$arProperty['CODE'];

				if (empty($arProperty['CODE']))
                    $arProperty['CODE'] = $arProperty['ID'];

				if (!ArrayHelper::keyExists($arProperty['CODE'], $arProperties)) {
                    $arProperties[$arProperty['CODE']] = [
						'CODE' => $arProperty['CODE'],
						'TITLE' => $arProperty['NAME'].' ['.$arProperty['CODE'].']',
						'ID' => [
                            $arProperty['ID']
                        ],
						'IBLOCK_ID' => [
                            $arProperty['IBLOCK_ID'] => $arProperty['IBLOCK_ID']
                        ],
						'IBLOCK_TITLE' => [
                            $arProperty['IBLOCK_ID'] => $arIBlockName[$arProperty['IBLOCK_ID']]
                        ],
						'COUNT' => 1
					];
				} else {
                    $arProperties[$arProperty['CODE']]['ID'][] = $arProperty['ID'];
                    $arProperties[$arProperty['CODE']]['IBLOCK_ID'][$arProperty['IBLOCK_ID']] = $arProperty['IBLOCK_ID'];

					if ($arProperties[$arProperty['CODE']]['COUNT'] < 2)
                        $arProperties[$arProperty['CODE']]['IBLOCK_TITLE'][$arProperty['IBLOCK_ID']] = $arIBlockName[$arProperty['IBLOCK_ID']];

                    $arProperties[$arProperty['CODE']]['COUNT']++;
				}
			}

			unset($arIBlockID, $arIBlockName, $rsProperties, $arProperty);

			if (!empty($arProperties) && Type::isArray($arProperties)) {
                foreach ($arProperties as $arProperty) {
                    $sIBlockList = '';

                    if ($arProperty['COUNT'] > 1)
                        $sIBlockList = $arProperty['COUNT'] > 2 ? ' ( ... )' : ' ('.implode(', ', $arProperty['IBLOCK_TITLE']).')';

                    $arPropertiesList['PROPERTY_'.$arProperty['CODE']] = $arProperty['TITLE'].$sIBlockList;
                }

                unset($arProperty, $sIBlockList);
            }

			unset($arProperties);
		}
	}

	if (!empty($arPropertiesList)) {
		$arTemplateParameters['BRAND_PROPERTY'] = [
			'PARENT' => 'ANALYTICS_SETTINGS',
			'NAME' => Loc::getMessage('C_BASKET_TEMPLATE_DEFAULT_2_BRAND_PROPERTY'),
			'TYPE' => 'LIST',
			'MULTIPLE' => 'N',
			'DEFAULT' => '',
			'VALUES' => ArrayHelper::merge(['' => ''], $arPropertiesList)
		];
	}

	unset($arPropertiesList);
}

$arTemplateParameters['ORDER_FAST_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_BASKET_TEMPLATE_DEFAULT_2_ORDER_FAST_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['ORDER_FAST_USE'] === 'Y') {
    include(__DIR__.'/parameters/order.fast.php');
}

if ($arCurrentValues['USE_GIFTS'] === 'Y') {
    $arTemplateParameters['GIFTS_MESS_BTN_BUY'] = ['HIDDEN' => 'Y'];
    $arTemplateParameters['GIFTS_MESS_BTN_DETAIL'] = ['HIDDEN' => 'Y'];
    $arTemplateParameters['GIFTS_PAGE_ELEMENT_COUNT'] = [
        'PARENT' => 'GIFTS',
        'NAME' => Loc::getMessage('C_BASKET_TEMPLATE_DEFAULT_2_GIFTS_PAGE_COUNT'),
        'TYPE' => 'STRING',
        'DEFAULT' => '4'
    ];
    $arTemplateParameters['GIFTS_COLUMNS'] = [
        'PARENT' => 'GIFTS',
        'NAME' => Loc::getMessage('C_BASKET_TEMPLATE_DEFAULT_2_GIFTS_COLUMNS'),
        'TYPE' => 'LIST',
        'VALUES' => [
            4 => '4',
            5 => '5'
        ],
        'DEFAULT' => 4
    ];

    $arTemplateParameters['GIFTS_SHOW_SLIDER'] = [
        'PARENT' => 'GIFTS',
        'NAME' => Loc::getMessage('C_BASKET_TEMPLATE_DEFAULT_2_GIFTS_SHOW_SLIDER'),
        'TYPE' => 'CHECKBOX',
        'REFRESH' => 'Y',
        'DEFAULT' => 'N'
    ];

    if (isset($arCurrentValues['GIFTS_SHOW_SLIDER']) && $arCurrentValues['GIFTS_SHOW_SLIDER'] === 'Y') {
        $arTemplateParameters['GIFTS_SLIDER_INTERVAL'] = [
            'PARENT' => 'GIFTS',
            'NAME' => Loc::getMessage('C_BASKET_TEMPLATE_DEFAULT_2_GIFTS_SLIDER_INTERVAL'),
            'TYPE' => 'STRING',
            'MULTIPLE' => 'N',
            'REFRESH' => 'N',
            'DEFAULT' => '3000'
        ];
        $arTemplateParameters['GIFTS_GIFTS_SLIDER_PROGRESS'] = [
            'PARENT' => 'GIFTS',
            'NAME' => Loc::getMessage('C_BASKET_TEMPLATE_DEFAULT_2_GIFTS_SLIDER_PROGRESS'),
            'TYPE' => 'CHECKBOX',
            'MULTIPLE' => 'N',
            'REFRESH' => 'N',
            'DEFAULT' => 'N'
        ];
    }
}

$arTemplateParameters['QUICK_VIEW_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_BASKET_TEMPLATE_DEFAULT_2_QUICK_VIEW_USE'),
    'TYPE' => 'CHECKBOX',
    'REFRESH' => 'Y',
    'DEFAULT' => 'N'
];

if ($arCurrentValues['QUICK_VIEW_USE'] === 'Y') {
    include(__DIR__ . '/parameters/quick.view.php');
}