<?php

namespace Sotbit\Splitter\Action;

class SplitAction
{
    public function __construct(
        private string $firstSplitProperty,
        private string $secondSplitProperty,
    ) {}

    public function handle(array $basketItems): array
    {
        $groupOrders = [];

        foreach ($basketItems as $basketItem) {

            $properties = $basketItem->getPropertyCollection()->getPropertyValues();

            $first = $properties[$this->firstSplitProperty]['VALUE'];
            $second = $properties[$this->secondSplitProperty]['VALUE'];

            $groupOrders[$first][$second][] = $basketItem;
           // $groupOrders[$first][$second][] = $basketItem->getId();
        }

        return array_reduce($groupOrders, function ($groups, $items) {
            return array_merge($groups, array_values($items));
        }, []);
    }
}