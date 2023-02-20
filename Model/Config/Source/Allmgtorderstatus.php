<?php

namespace Bsitc\Brightpearl\Model\Config\Source;

class Allmgtorderstatus implements \Magento\Framework\Option\ArrayInterface
{

    public function toOptionArray()
    {
        $arr = $this->toArray();
        $ret = [];
        foreach ($arr as $key => $value) {
            $ret[] = [
                'value' => $key,
                'label' => $value
            ];
        }
        return $ret;
    }
    
    public function toArray()
    {
        return $this->getSatus();
    }
    
    public function getSatus()
    {
    
		$option = [];
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$orderStatusCollectionFactory = $objectManager->create('\Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory');
		$collection = $orderStatusCollectionFactory->create()->toOptionArray();
        if (count($collection)>0) {
            foreach ($collection as $item) {
				 $value = $item['value'];
				 $label = $item['label'];
				 $option[$value] = $label;
            }
        }
        return $option;
    }
 	
	
	

}


 