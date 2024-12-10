<?php

use Bitrix\Main\DI\ServiceLocator;
use Sotbit\Splitter\Action\SplitAction;
use Sotbit\Splitter\Action\CloneAction;
use Bitrix\Main\Config\Option;


$firstProperty = Option::get('sotbit.splitter', 'FIRST_PROPERTY', '');
$secondProperty = Option::get('sotbit.splitter', 'SECOND_PROPERTY', '');

$container = ServiceLocator::getInstance();

$container->addInstanceLazy(SplitAction::class, [
    'constructor' => function () use ($firstProperty, $secondProperty) {
        return new SplitAction($firstProperty, $secondProperty);
    }
]);

$container->addInstanceLazy(CloneAction::class, [
    'constructor' => function () {
        return new CloneAction();
    }
]);
