<?php

namespace Bsitc\Brightpearl\Observer;

class OrderSuccess implements \Magento\Framework\Event\ObserverInterface
{

    protected $_objectManager;
    protected $_storemanager;
    protected $_orderqueuefactory;
    protected $_helperdata;
    
 
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Bsitc\Brightpearl\Model\OrderqueueFactory $orderqueuefactory,
        \Bsitc\Brightpearl\Model\SalesorderreportFactory $salesorderreportFactory,
        \Bsitc\Brightpearl\Helper\Data $helperdata
    ) {
        $this->_objectManager              = $objectManager;
        $this->_storemanager               = $storeManager;
        $this->_orderqueuefactory          = $orderqueuefactory;
        $this->_salesorderreportFactory    = $salesorderreportFactory;
        $this->_helperdata                 = $helperdata;
    }
    
    public function alreadyExits($orderid)
    {
        
        $collection = $this->_orderqueuefactory->create()->getCollection()->addFieldToFilter('order_id', $orderid);
        if (count($collection)>0) {
            return true;
        } else {
            return false;
        }
    }
    
    public function alreadySend($order)
    {
        
         $collection = $this->_salesorderreportFactory->create()->getCollection()->addFieldToFilter('mgt_order_id', $order->getIncrementId());
        if (count($collection)>0) {
            return true;
        } else {
            return false;
        }
    }
     
    public function skipOldOrder($order)
    {
    
    
         $created_at = $order->getCreatedAt();
        $configureDate = $this->_helperdata->getConfig('bpconfiguration/bp_orderconfig/skiporderfrom');
        if ($configureDate) {
            $configDateTimestamp     = strtotime($configureDate);
            $orderDateTimestamp     = strtotime($created_at);
            if ($orderDateTimestamp > $configDateTimestamp) {
                return false;
            } else {
                $log = $this->_objectManager->create('\Bsitc\Brightpearl\Model\LogsFactory');
                $msg = "Skip Old Order = Configure Date : ".$configureDate.'  Order Create At : '.$created_at;
                $log->recordLog($msg, $order->getIncrementId(), "Order");
                return true;
            }
        } else {
            $log = $this->_objectManager->create('\Bsitc\Brightpearl\Model\LogsFactory');
            $msg = "Please set the order post starting date in configuration";
            $log->recordLog($msg, $order->getIncrementId(), "Order");
             return false;
        }
    }
    

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
            
        // -------- Check if BP module are enable ------------/
        if ($this->_helperdata->getBrightpearlEnable()) {
            if ($this->_helperdata->getOrderqueueEnable()) {
                $order             = $observer->getEvent()->getOrder();
                $orderid         = $order->getId();
                $incrementid     = $order->getIncrementId();
                
                 //---- skip old order that are edit by thirdpart extesion and fire event based on configure date ---
                if ($this->skipOldOrder($order)) {
                    return $this;
                }
                
                 //---- Check if order already sentexist in sent order report ------
                if ($this->alreadySend($order)) {
                    return $this;
                }
                 
                 //---- Check if order already exits in Queue ------
                if ($this->alreadyExits($orderid)) {
                    return $this;
                }
                  
                $data                     = [];
                $data['order_id']         =  $orderid;
                $data['increment_id']     =  $incrementid;
                $data['state']             =  $this->_orderqueuefactory->pendingState;
                //  ---- add order in queue ------
                $this->_orderqueuefactory->addRecord($data);
                return $this;
            }
        }
    }
}
