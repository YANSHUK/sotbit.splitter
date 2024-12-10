<?php

namespace Sotbit\Splitter\Action;

use Bitrix\Sale\Basket;
use Bitrix\Sale\Order;

class CloneAction
{
    public function handle(Order $order, Basket $splitBasket)
    {
        $paymentCollection = $order->getPaymentCollection();
        foreach ($paymentCollection as $payment) {
            $paySysID = $payment->getPaymentSystemId();
            $paySysName = $payment->getPaymentSystemName();
        }


        $shipmentCollection = $order->getShipmentCollection();
        foreach ($shipmentCollection as $shipment) {
            if ($shipment->isSystem()) continue;
            $shipID = $shipment->getField('DELIVERY_ID');
            $shipName = $shipment->getField('DELIVERY_NAME');
        }

        $orderNew = \Bitrix\Sale\Order::create($order->getSiteId(), $order->getUserId());

        $orderNew->setField('CURRENCY', $order->getCurrency());

        $orderNew->setPersonTypeId($order->getPersonTypeId());

        $orderNew->setBasket($splitBasket);

        $shipmentCollectionNew = $orderNew->getShipmentCollection();
        $shipmentNew = $shipmentCollectionNew->createItem();
        $shipmentNew->setFields(
            array(
                'DELIVERY_ID' => $shipID,
                'DELIVERY_NAME' => $shipName,
                'CURRENCY' => $order->getCurrency()
            )
        );

        $shipmentCollectionNew->calculateDelivery();

        $paymentCollectionNew = $orderNew->getPaymentCollection();
        $paymentNew = $paymentCollectionNew->createItem();
        $paymentNew->setFields(
            array(
                'PAY_SYSTEM_ID' => $paySysID,
                'PAY_SYSTEM_NAME' => $paySysName
            )
        );

        $propertyCollection = $order->getPropertyCollection();
        $propertyCollectionNew = $orderNew->getPropertyCollection();

        foreach ($propertyCollection as $property) {

            $somePropValue = $propertyCollectionNew->getItemByOrderPropertyId($property->getPropertyId());

            $somePropValue->setValue($property->getField('VALUE'));
        }

        $orderNew->doFinalAction(true);
        return $orderNew->save();
    }
}