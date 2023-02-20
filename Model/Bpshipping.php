<?php

namespace Bsitc\Brightpearl\Model;

class Bpshipping extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Bsitc\Brightpearl\Model\ResourceModel\Bpshipping');
    }

    public function getBpShippinngOptionArray()
    {
        $option = [];
        $collection = $this->getCollection();
        if (count($collection)>0) {
            foreach ($collection as $item) {
                $option[$item['bpid']] = $item['bpname'];
            }
        }
        return $option;
    }
     
    
    public function getMgtShippinngOptionArray()
    {

        $obj = \Magento\Framework\App\ObjectManager::getInstance();
          $shipingObj = $obj->create('\Magento\Shipping\Model\Config');
          $scopeConfigObj = $obj->create('\Magento\Framework\App\Config\ScopeConfigInterface');
            
          $activeCarriers = $shipingObj->getActiveCarriers();
          $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
          $methods = [];
        foreach ($activeCarriers as $carrierCode => $carrierModel) {
            $options = [];
            if ($carrierMethods = $carrierModel->getAllowedMethods()) {
                foreach ($carrierMethods as $methodCode => $method) {
                //$code      =  $methodCode;
                    $code    = $carrierCode.'_'.$methodCode;
                    $options   = $code;
                    break;
                }
                      $carrierTitle =$scopeConfigObj->getValue('carriers/'.$carrierCode.'/title');
            }
                  $methods[] = ['value' => $options, 'label' =>  $carrierTitle];
        }

              //return $methods;

              $finalarray = [];
                
        foreach ($methods as $data) {
            if (!is_array($data['value'])) {
                  $value  = $data['value'];
                  $label  = $data['label'];
                  $finalarray[$value] = $label;
            }
        }

              return $finalarray;
    }
}
