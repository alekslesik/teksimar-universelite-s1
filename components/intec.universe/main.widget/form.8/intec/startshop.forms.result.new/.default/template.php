<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\Core;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;

if (!Loader::includeModule('intec.startshop'))
    return;

/**
 * @var array $arResult
 * @var CMain $APPLICATION
 */

$request = Core::$app->request;

$this->setFrameMode(true);

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));
$arVisual = $arResult['VISUAL'];
$arVariables = ArrayHelper::getValue($arResult, 'VARIABLES');
$sCaptchaCode = null;
$arCaptcha = [];

if ($arResult['USE_CAPTCHA'] == 'Y') {
    $sCaptchaCode = $APPLICATION->CaptchaGetCode();

    $arCaptcha = [
        'img' => [
            'width' => 180,
            'height' => 40
        ]
    ];
}

$sSubmitText = ArrayHelper::getValue($arResult, ['LANG', LANGUAGE_ID, 'BUTTON']);
$sFormAction = $APPLICATION->GetCurPage();
$sFormId = 'form_$sTemplateId';
$arForm = [
    'id' => $sFormId,
    'enctype' => 'multipart/form-data'
];

?>
<div class="ns-intec c-form-result-new c-form-result-new-form-8" id="<?= $sTemplateId ?>">
    <?= Html::beginTag('div', [
        'class' => 'form-result-new-wrapper',
        'data-parallax-ratio' => $arVisual['BACKGROUND']['PARALLAX']['USE'] ? $arVisual['BACKGROUND']['PARALLAX']['RATIO'] : null,
        'data' => [
            'lazyload-use' => $arVisual['LAZYLOAD']['USE'] && !empty($arVisual['BACKGROUND']['PATH']) ? 'true' : 'false',
            'original' => $arVisual['LAZYLOAD']['USE'] && !empty($arVisual['BACKGROUND']['PATH']) ? $arVisual['BACKGROUND']['PATH'] : null
        ],
        'style' => [
            'background-image' => !$arVisual['LAZYLOAD']['USE'] && !empty($arVisual['BACKGROUND']['PATH']) ? 'url(\''.$arVisual['BACKGROUND']['PATH'].'\')' : null
        ]
    ]) ?>
        <div class="intec-content">
            <div class="intec-content-wrapper">
                <?= Html::beginTag('div', [
                    'class' => Html::cssClassFromArray([
                        'intec-grid' => [
                            '' => true,
                            'wrap' => true,
                            'a-v-stretch' => true,
                            'a-h-start' => $arVisual['FORM']['POSITION'] != 'center',
                            'a-h-center' => $arVisual['FORM']['POSITION'] == 'center',
                            'o-horizontal-reverse' => $arVisual['FORM']['POSITION'] == 'right',
                            'i-h-25' => true
                        ]
                    ], true)
                ]) ?>
                    <div class="intec-grid-item-2 intec-grid-item-768-1">
                        <div class="form-result-new-content intec-ui-form">
                            <?php if ($arVisual['FORM']['TITLE']['SHOW']) { ?>
                                <div class="form-result-new-header">
                                    <div class="form-result-new-title">
                                        <?= $arVisual['FORM']['TITLE']['VALUE'] ?>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php if (($arResult['ERROR']['CODE'] == 0 || $arResult['ERROR']['CODE'] >= 4) && !$arResult['SENT']) { ?>
                                <?= Html::beginForm($sFormAction, 'post', $arForm) ?>
                                    <?= Html::hiddenInput($arVariables['REQUEST_VARIABLE_ACTION'], 'send') ?>
                                    <div class="form-result-new-fields intec-ui-form-fields">
                                        <?php foreach ($arResult['PROPERTIES'] as $iKey => $arProperty) {
                                            $bRequired = ArrayHelper::getValue($arProperty, 'REQUIRED') == 'Y';
                                            $sFieldName = ArrayHelper::getValue($arProperty, 'CODE');
                                            $sFieldName = Html::encode($sFieldName);
                                            $sFieldCaption = ArrayHelper::getValue($arProperty, ['LANG', LANGUAGE_ID, 'NAME']);
                                            $bFieldActive = ArrayHelper::getValue($arProperty, 'READONLY') == 'Y';
                                            ?>
                                            <div class="form-result-new-element">
                                                <?php if ($arProperty['TYPE'] == STARTSHOP_FORM_PROPERTY_TYPE_TEXT) { ?>
                                                    <?= Html::beginTag('label', [
                                                        'class' => Html::cssClassFromArray([
                                                            'form-result-new-field' => true,
                                                            'intec-ui-form-field' => true,
                                                            'intec-ui-form-field-required' => $arProperty['REQUIRED'] === 'Y'
                                                        ], true)
                                                    ]) ?>
                                                        <span class="form-result-new-field-title intec-ui-form-field-title">
                                                            <?= $sFieldCaption ?>
                                                        </span>
                                                        <span class="form-result-new-field-content intec-ui-form-field-content">
                                                            <span class="form-result-new-field-title-mobile intec-ui-form-field-title">
                                                                <?= $sFieldCaption ?>
                                                            </span>
                                                            <?= Html::input('text', $sFieldName, $request->post($sFieldName), [
                                                                'class' => [
                                                                    'inputtext',
                                                                    'intec-ui' => [
                                                                        '',
                                                                        'control-input',
                                                                        'mod-block',
                                                                        'mod-round-3',
                                                                        'size-2'
                                                                    ]
                                                                ],
                                                                'disabled' => $bFieldActive ? 'disabled' : null,
                                                                'placeholder' => ''
                                                            ]) ?>
                                                        </span>
                                                    <?= Html::endTag('label') ?>
                                                    <?php if ($arResult['ERROR']['CODE'] == 5) { ?>
                                                        <?php if (ArrayHelper::keyExists($sFieldName, $arResult['ERROR']['FIELDS']['EMPTY'])) {
                                                            $sErrorText = Loc::getMessage('C_FORM_RESULT_NEW_TEMPLATE_8_TEMPLATE_ERROR_EMPTY');
                                                        } else if (ArrayHelper::keyExists($sFieldName, $arResult['ERROR']['FIELDS']['INVALID'])) {
                                                            $sErrorText = Loc::getMessage('C_FORM_RESULT_NEW_TEMPLATE_8_TEMPLATE_ERROR_INVALID');
                                                        } ?>
                                                        <?php if (!empty($sErrorText)) { ?>
                                                        <?= Html::tag('div', $sErrorText, [
                                                            'class' => [
                                                                'form-result-new-error',
                                                                'intec-ui' => [
                                                                    '',
                                                                    'control-alert',
                                                                    'scheme-red',
                                                                    'm-v-10'
                                                                ]
                                                            ]
                                                        ]) ?>
                                                        <?php } ?>
                                                        <?php unset($sErrorText) ?>
                                                    <?php } ?>
                                                <?php } ?>
                                                <?php if ($arProperty['TYPE'] == STARTSHOP_FORM_PROPERTY_TYPE_TEXTAREA) { ?>
                                                    <?= Html::beginTag('label', [
                                                        'class' => Html::cssClassFromArray([
                                                            'form-result-new-field' => true,
                                                            'intec-ui-form-field' => true,
                                                            'intec-ui-form-field-required' => $arProperty['REQUIRED'] === 'Y'
                                                        ], true)
                                                    ]) ?>
                                                        <span class="form-result-new-field-title intec-ui-form-field-title">
                                                            <?= $sFieldCaption ?>
                                                        </span>
                                                        <span class="form-result-new-field-content intec-ui-form-field-content">
                                                            <span class="form-result-new-field-title-mobile intec-ui-form-field-title">
                                                                <?= $sFieldCaption ?>
                                                            </span>
                                                            <?= Html::textarea($sFieldName, $request->post($sFieldName), [
                                                                'class' => [
                                                                    'inputtextarea',
                                                                    'intec-ui' => [
                                                                        '',
                                                                        'control-input',
                                                                        'mod-block',
                                                                        'mod-round-3',
                                                                        'size-2'
                                                                    ]
                                                                ],
                                                                'cols' => 40,
                                                                'rows' => 5,
                                                                'disabled' => $bFieldActive ? 'disabled' : null,
                                                                'placeholder' => ''
                                                            ]) ?>
                                                        </span>
                                                    <?= Html::endTag('label') ?>
                                                    <?php if ($arResult['ERROR']['CODE'] == 5) { ?>
                                                        <?php if (ArrayHelper::keyExists($sFieldName, $arResult['ERROR']['FIELDS']['EMPTY'])) { ?>
                                                            <?= Html::tag('div', Loc::getMessage('C_FORM_RESULT_NEW_TEMPLATE_8_TEMPLATE_ERROR_EMPTY'), [
                                                                'class' => [
                                                                    'form-result-new-error',
                                                                    'intec-ui' => [
                                                                        '',
                                                                        'control-alert',
                                                                        'scheme-red',
                                                                        'm-v-10'
                                                                    ]
                                                                ]
                                                            ]) ?>
                                                        <?php } ?>
                                                    <?php } ?>
                                                <?php } ?>
                                            </div>
                                        <?php } ?>
                                        <?php if ($arVisual['CAPTCHA']['USE']) { ?>
                                            <div class="form-result-new-field form-result-new-field-captcha intec-ui-form-field intec-ui-form-field-required">
                                                <div class="form-result-new-field-title intec-ui-form-field-title">
                                                    <?= Loc::getMessage('C_FORM_RESULT_NEW_TEMPLATE_8_TEMPLATE_FIELDS_CAPTCHA') ?>
                                                </div>
                                                <div class="form-result-new-field-content intec-ui-form-field-content">
                                                    <?= Html::hiddenInput($arVariables['FORM_VARIABLE_CAPTCHA_SID'], $sCaptchaCode) ?>
                                                    <div class="form-result-new-captcha intec-grid intec-grid-nowrap intec-grid-500-wrap intec-grid-i-h-5">
                                                        <div class="form-result-new-captcha-image intec-grid-item-auto intec-grid-item-500-1">
                                                            <?= Html::img('/bitrix/tools/captcha.php?captcha_sid=' . $sCaptchaCode, $arCaptcha['img']) ?>
                                                        </div>
                                                        <div class="form-result-new-captcha-input intec-grid-item intec-grid-item-500-1">
                                                            <?= Html::input('text', $arVariables['FORM_VARIABLE_CAPTCHA_CODE'], null, [
                                                                'class' => [
                                                                    'intec-ui' => [
                                                                        '',
                                                                        'control-input',
                                                                        'mod-block',
                                                                        'mod-round-3',
                                                                        'size-2'
                                                                    ]
                                                                ]
                                                            ]) ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php if ($arResult['ERROR']['CODE'] == 4) { ?>
                                                    <?= Html::tag('div', Loc::getMessage('C_FORM_RESULT_NEW_TEMPLATE_8_TEMPLATE_ERROR_CAPTCHA'), [
                                                        'class' => [
                                                            'form-result-new-error',
                                                            'intec-ui' => [
                                                                '',
                                                                'control-alert',
                                                                'scheme-red',
                                                                'm-v-10'
                                                            ]
                                                        ]
                                                    ]) ?>
                                                <?php } ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <?php if ($arVisual['CONSENT']['SHOW']) { ?>
                                        <div class="form-result-new-consent">
                                            <label class="intec-ui intec-ui-control-checkbox intec-ui-scheme-current">
                                                <?= Html::checkbox('licenses_popup', $arVisual['CONSENT']['CHECKED'], [
                                                    'required' => true
                                                ]) ?>
                                                <span class="intec-ui-part-selector"></span>
                                                <span class="intec-ui-part-content">
                                                    <?= Loc::getMessage('C_FORM_RESULT_NEW_TEMPLATE_8_TEMPLATE_CONSENT', [
                                                        '#URL#' => $arVisual['CONSENT']['URL']
                                                    ]) ?>
                                                </span>
                                            </label>
                                        </div>
                                    <?php } ?>
                                    <div class="form-result-new-buttons">
                                        <?= Html::submitButton($sSubmitText, [
                                            'class' => [
                                                'form-result-new-button',
                                                'intec-ui' => [
                                                    '',
                                                    'control-button',
                                                    'scheme-current',
                                                    'mod-round-2'
                                                ]
                                            ],
                                            'disabled' => $arVisual['CONSENT']['SHOW']
                                        ]) ?>
                                    </div>
                                <?= Html::endForm() ?>
                            <?php } else if ($arResult['SENT']) { ?>
                                <div class="form-result-new-sent">
                                    <?= $arResult['LANG'][LANGUAGE_ID]['SENT'] ?>
                                </div>
                            <?php } else { ?>
                                <div class="form-result-new-message">
                                    <div class="form-result-message-error intec-ui intec-ui-control-alert intec-ui-scheme-red">
                                        <?php if ($arResult['ERROR']['CODE'] == 1) { ?>
                                            <?= Loc::getMessage('C_FORM_RESULT_NEW_TEMPLATE_8_TEMPLATE_ERROR_FORM_NOT_EXIST') ?>
                                        <?php } else if ($arResult['ERROR']['CODE'] == 2) { ?>
                                            <?= Loc::getMessage('C_FORM_RESULT_NEW_TEMPLATE_8_TEMPLATE_ERROR_FORM_UNBOUND') ?>
                                        <?php } else if ($arResult['ERROR']['CODE'] == 3) { ?>
                                            <?= Loc::getMessage('C_FORM_RESULT_NEW_TEMPLATE_8_TEMPLATE_ERROR_FORM_NO_FIELDS') ?>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>


                    <?php if ($arVisual['ADDITIONAL_PICTURE']['SHOW']) { ?>
                        <div class="intec-grid-item-2 intec-grid-item-768-1 form-result-new-additional-picture-wrapper">
                            <?= Html::tag('div', '', [
                                'class' => 'form-result-new-additional-picture',
                                'style' => [
                                    'background-image' => !$arVisual['LAZYLOAD']['USE'] && !empty($arVisual['ADDITIONAL_PICTURE']['PATH']) ? 'url(\''.$arVisual['ADDITIONAL_PICTURE']['PATH'].'\')' : null,
                                    'background-position-x' => 'center',
                                    'background-position-y' => $arVisual['ADDITIONAL_PICTURE']['VERTICAL_ALIGN'],
                                    'background-size' => $arVisual['ADDITIONAL_PICTURE']['SIZE']
                                ],
                                'data' => [
                                    'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                    'original' => $arVisual['LAZYLOAD']['USE'] && !empty($arVisual['ADDITIONAL_PICTURE']['PATH']) ? $arVisual['ADDITIONAL_PICTURE']['PATH'] : null
                                ]
                            ]) ?>
                        </div>
                    <?php } ?>
                <?= Html::endTag('div') ?>
            </div>
        </div>
    <?= Html::endTag('div') ?>
    <script type="text/javascript">
        template.load(function (data) {
            var $ = this.getLibrary('$');
            var component = {};

            component.form = $('form', data.nodes);
            component.consent = $('[name="licenses_popup"]', component.form);
            component.submit = $('[type="submit"]', component.form);

            if (!component.form.length || !component.consent.length || !component.submit.length)
                return;

            component.handler = {
                'change': function () {
                    component.submit.prop('disabled', !component.consent.prop('checked'));
                },
                'submit': function () {
                    return component.consent.prop('checked');
                }
            };

            component.form.on('submit', component.handler.submit);
            component.consent.on('change', component.handler.change);

            component.handler.change();
        }, {
            'name': '[Component] intec.universe:main.widget (form.8)',
            'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
            'loader': {
                'name': 'lazy'
            }
        });
    </script>
</div>