<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Iblock\PropertyTable;

$module_id = 'sotbit.splitter';

Loader::includeModule('iblock');
Loader::includeModule($module_id);

Loc::loadMessages(__FILE__);

if ($APPLICATION->GetGroupRight($module_id) < "R") {
    $APPLICATION->AuthForm(Loc::getMessage("ACCESS_DENIED"));
}


$propertyList = [];

$res = PropertyTable::getList([
    'select' => ['ID', 'NAME', 'CODE', 'IBLOCK_ID'],
    'filter' => ['ACTIVE' => 'Y', 'PROPERTY_TYPE' => 'L'],
    'order' => ['IBLOCK_ID' => 'ASC', 'NAME' => 'ASC'],
]);

while ($property = $res->fetch()) {
    $propertyList[$property['CODE']] = "[{$property['IBLOCK_ID']}] {$property['NAME']} ({$property['CODE']})";
}

$options = [
    [
        "FIRST_PROPERTY",
        Loc::getMessage("SOTBIT_SPLITTER_FIRST_PROPERTY"),
        Option::get($module_id, "FIRST_PROPERTY", ""),
        ["selectbox", $propertyList],
    ],
    [
        "SECOND_PROPERTY",
        Loc::getMessage("SOTBIT_SPLITTER_SECOND_PROPERTY"),
        Option::get($module_id, "SECOND_PROPERTY", ""),
        ["selectbox", $propertyList],
    ],
];

$aTabs = [
    [
        'DIV' => 'settings',
        'TAB' => Loc::getMessage("SOTBIT_SPLITTER_TAB_NAME"),
        "TITLE" => Loc::getMessage("SOTBIT_SPLITTER_TITLE"),
        'OPTIONS' => $options,
    ]
];

$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();

if ($request->isPost() && $request['Update'] && check_bitrix_sessid()) {
    foreach ($aTabs as $aTab) {
        foreach ($aTab['OPTIONS'] as $arOption) {
            $optionName = $arOption[0];
            $optionValue = $request->getPost($optionName);
            Option::set($module_id, $optionName, $optionValue);
        }
    }
}

$tabControl = new CAdminTabControl('tabControl', $aTabs);

?>

<?php $tabControl->Begin(); ?>
<form method='post'
      action='<?= $APPLICATION->GetCurPage() ?>?mid=<?= htmlspecialcharsbx($request['mid']) ?>&amp;lang=<?= $request['lang'] ?>'
      name='SOTBIT_SPLITTER_SETTINGS'>

    <?php foreach ($aTabs as $aTab): ?>
        <?php if ($aTab['OPTIONS']): ?>
            <?php $tabControl->BeginNextTab(); ?>
            <?php __AdmSettingsDrawList($module_id, $aTab['OPTIONS']); ?>
        <?php endif; ?>
    <?php endforeach; ?>

    <?php $tabControl->Buttons(); ?>
    <input type="submit" name="Update" value="<?= Loc::getMessage('MAIN_SAVE') ?>" class="adm-btn-save">
    <?= bitrix_sessid_post(); ?>
</form>
<?php $tabControl->End(); ?>
