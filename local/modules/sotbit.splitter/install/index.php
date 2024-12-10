<?php

use Bitrix\Main\ModuleManager;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\EventManager;

Loc::loadMessages(__FILE__);

class sotbit_splitter extends CModule
{
    public $MODULE_ID = 'sotbit.splitter';
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;

    public function __construct()
    {
        $arModuleVersion = [];
        include(__DIR__ . '/version.php');

        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        $this->MODULE_NAME = GetMessage('SOTBIT_FORM_MODULE_NAME');
        $this->MODULE_DESCRIPTION = GetMessage('SOTBIT_FORM_MODULE_DESCRIPTION');
        $this->PARTNER_NAME = GetMessage('SOTBIT_FORM_PARTNER_NAME');
        $this->PARTNER_URI = GetMessage('SOTBIT_FORM_PARTNER_URI');
    }

    public function DoInstall(): void
    {
        $this->InstallEvents();
        ModuleManager::registerModule($this->MODULE_ID);
    }

    public function DoUninstall(): void
    {
        $this->UnInstallEvents();
        ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    public function InstallEvents(): void
    {
        EventManager::getInstance()
            ->registerEventHandler(
                'sale',
                'OnSaleOrderSaved',
                $this->MODULE_ID,
                Sotbit\Splitter\Listener\ProcessOrderSave::class,
                'handle',
            );
    }

    public function UnInstallEvents(): void
    {
        EventManager::getInstance()
            ->unRegisterEventHandler(
                'sale',
                'OnSaleOrderSaved',
                $this->MODULE_ID,
                Sotbit\Splitter\Listener\ProcessOrderSave::class,
                'handle',
            );
    }

}
