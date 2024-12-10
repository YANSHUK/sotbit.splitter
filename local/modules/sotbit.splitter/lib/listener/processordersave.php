<?php

namespace Sotbit\Splitter\Listener;

use Bitrix\Main\DI\ServiceLocator;
use Bitrix\Main\Event;
use Bitrix\Sale\Basket;
use Bitrix\Sale\Order;
use Sotbit\Splitter\Action\CloneAction;
use Sotbit\Splitter\Action\SplitAction;

/**
 * Module: sale
 * Event: OnSaleOrderBeforeSaved
 */
class ProcessOrderSave
{

    private static array $processedOrders = []; //костыль

    public static function handle(Event $orderEvent): void
    {
        $order = $orderEvent->getParameter('ENTITY');

        if (in_array($order->getId(), self::$processedOrders)) {
            return;
        }

        self::$processedOrders[] = $order->getId();

        $splitBasketAction = ServiceLocator::getInstance()->get(SplitAction::class);
        $orderCloneAction = ServiceLocator::getInstance()->get(CloneAction::class);

        $groups = $splitBasketAction->handle($order->getBasket()->getBasketItems());

        if (count($groups) === 1) {
            return;
        }

        foreach ($groups as $group) {

            $splitBasket = Basket::create(SITE_ID);

            foreach ($group as $item) {
                $splitItem = $splitBasket->createItem('catalog', $item->getProductId());

                $splitItem->setFields([
                    'QUANTITY' => $item->getQuantity(),
                    'PRICE' => $item->getPrice(),
                    'CURRENCY' => $item->getCurrency(),
                    'NAME' => $item->getField('NAME'),
                ]);
            }

            $orderCloneAction->handle($order, $splitBasket);
        }

        Order::delete($order->getId());
    }
}
