<?php

namespace Bsitc\Brightpearl\Model;

class OrderstatusupdatemappingFactory
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create new country model
     *
     * @param array $arguments
     * @return \Magento\Directory\Model\Country
     */
    public function create(array $arguments = [])
    {
        return $this->_objectManager->create('Bsitc\Brightpearl\Model\Orderstatusupdatemapping', $arguments, false);
    }
	
    public function addRecord($row)
    {
        if (count($row)>0) {
            $record = $this->create();
            $record->setData($row);
            $record->save();
        }
        return true;
    }
    
    public function updateRecord($id, $row)
    {
        $record =  $this->create()->load($id);
         $record->setData($row);
        $record->setId($id);
        $record->save();
    }
    
    public function findRecord($column, $value)
    {
        
        $data = '';
        $collection = $this->create()->getCollection()->addFieldToFilter($column, $value);
        if ($collection->getSize()) {
            $data  = $collection->getFirstItem();
        }
        return $data;
    }

    public function removeAllRecord()
    {
        $collection = $this->create()->getCollection();
        $collection->walk('delete');
        return true;
    }
    
    public function removeRecord($id)
    {
        $record = $this->create()->load($id);
        if ($record) {
            $record->delete();
        }
        return true;
    }
	
	
	public function updateMgtOrderStatus($mgtOrderIncrementId, $bpOrderStatus )
	{
		 if ($bpOrderStatus and $mgtOrderIncrementId ) {
			 $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			 $order = $this->_objectManager->get('Magento\Sales\Model\Order')->loadByIncrementId($mgtOrderIncrementId);
			 if($order)
			 {
				 $data = $this->findRecord('bpo_status_id',$bpOrderStatus);
				  if ($data) 
 				  {
					  $mgtOrderStatus = $data->getMgtoStatusCode();
					  $this->changeOrderStatus($order,$mgtOrderStatus);
					  $logManager = $this->_objectManager->create('\Bsitc\Brightpearl\Model\LogsFactory');
					  $logMsg = "Order status update successfully";
					  $logManager->recordLog($logMsg , "update order status", "Order Id ".$mgtOrderIncrementId);

 				  }
 			 }
 		 }
		 return true;
	}

	public function changeOrderStatus($order, $mgtOrderStatus ){            
		   $order->setStatus($mgtOrderStatus);
		   $order->save(); 
		   $history = $order->addStatusHistoryComment('BP Order stauts Updated : '.$mgtOrderStatus, $order->getStatus());
		   $history->setIsCustomerNotified(false);
		   $history->save(); 
	}
	
}