<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\JavaScript;

/**
 * @var array $arResult
 * @var string $sTemplateId
 */

$arForm = [
    'id' => $arResult['FORM']['ID'],
    'template' => $arResult['DATA']['FORM']['TEMPLATE'],
    'parameters' => [
        'AJAX_OPTION_ADDITIONAL' => $sTemplateId.'_FORM',
        'CONSENT_URL' => null
    ],
    'settings' => [
        'title' => $arResult['DATA']['FORM']['TITLE']
    ]
];

if ($arResult['DATA']['CONSENT']['SHOW'])
    $arForm['parameters']['CONSENT_USE'] = $arResult['DATA']['CONSENT']['URL'];

if (empty($arForm['settings']['title']))
    $arForm['settings']['title'] = Loc::getMessage('C_WIDGET_FORM_5_FORM_TITLE');

?>
<script type="text/javascript">
    template.load(function (data) {
        var app = this;
        var $ = app.getLibrary('$');
        var form = {
            'node': $('[data-role="form"]', data.nodes),
            'parameters': <?= JavaScript::toObject($arForm) ?>
        };

        form.node.on('click', function () {
            app.api.forms.show(form.parameters);
            app.metrika.reachGoal('forms.open');
            app.metrika.reachGoal(<?= JavaScript::toObject('forms.'.$arResult['FORM']['ID'].'.open')?>);
        });
    }, {
        'name': '[Component] intec.universe:main.widget (form.5)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>