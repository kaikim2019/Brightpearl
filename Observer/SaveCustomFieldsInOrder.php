<?php

namespace Bsitc\Brightpearl\Observer;

class SaveCustomFieldsInOrder implements \Magento\Framework\Event\ObserverInterface
{

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $quote = $observer->getEvent()->getQuote();
        $order->setData("warehouse_store", $quote->getWarehouseStore());
        return $this;
    }
}
