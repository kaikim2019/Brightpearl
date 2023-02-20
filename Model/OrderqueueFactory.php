<?php

namespace Bsitc\Brightpearl\Model;

class OrderqueueFactory
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    public $_date;
    public $_objectManager;
    public $_storeManager;
    public $_scopeConfig;
    public $_queuestatus;
    public $_salesorderreport;
    
    public $pendingState;
    public $processingState;
    public $completeState;
    public $errorState;
    public $errorFailed;
    public $errorNotPaid;
    
    
    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
     
     
     
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Bsitc\Brightpearl\Model\Queuestatus $queuestatus,
        \Bsitc\Brightpearl\Model\Salesorderreport $salesorderreport
    ) {
        $this->_date                 = $date;
        $this->_objectManager         = $objectManager;
        $this->_storeManager         = $storeManager;
         $this->_queuestatus         = $queuestatus;
         $this->_salesorderreport     = $salesorderreport;

        $queue_status                = $this->_queuestatus->getQueueOptionArray();
        $this->pendingState            = $queue_status['Pending'];
        $this->processingState        = $queue_status['Processing'];
        $this->completeState        = $queue_status['Completed'];
        $this->errorState            = $queue_status['Error'];
        $this->errorFailed            = $queue_status['Failed'];
        $this->errorNotPaid            = $queue_status['Not_Paid'];
    }

    /**
     * Create new country model
     *
     * @param array $arguments
     * @return \Magento\Directory\Model\Country
     */
    public function create(array $arguments = [])
    {
        return $this->_objectManager->create('Bsitc\Brightpearl\Model\Orderqueue', $arguments, false);
    }
    
    
    public function setOrderQueue($data)
    {
        if ($data['order_id']) {
            $this->addRecord($data);
        }
    }

    public function addRecord($data)
    {
        $orderdata  = $this->create();
        $orderdata->setData($data);
        $orderdata->save();
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
    
    
    public function processQueue()
    {
        
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        
        $this->cleanProcesQueue(); // ----- remove complete and not paid satus record from queue
        
        $this->updateStuckQueueRecord();  // ------- update stuck queue records
        if ($this->checkQueueProcessingStatus()) { // ------- check previous cron running or not
            return true;
        }
        $collection =   $this->create()->getCollection();
        $collection->addFieldToFilter('state', ['eq'=>$this->pendingState]);
        if (count($collection) > 0) {
            /* --------  update state in processing state  ---------------*/
            foreach ($collection as $item) {
                $item->setState($this->processingState)->save();
            }
            
            $_salesOrderHelper = $this->_objectManager->create('Bsitc\Brightpearl\Helper\SalesOrder');
            /* --------  start processing  ---------------*/
            foreach ($collection as $item) {
                $updateDataArray     = [];

                $updateDataArray ['state'] = $this->errorState ;
                $this->updateRecord($item->getId(), $updateDataArray);
                 
                $orderId             = $item->getOrderId();
                $incrementId         = $item->getIncrementId();
                if ($orderId) {
                    $response    = $_salesOrderHelper->CreateOrder($orderId, $incrementId);
                    if ($response) {
                        if ($response == "notpaid") {
                            $updateDataArray ['state'] = $this->errorNotPaid ;
                            $this->updateRecord($item->getId(), $updateDataArray);
                        } else {
                            $updateDataArray ['state'] = $this->completeState ;
                            $this->updateRecord($item->getId(), $updateDataArray);
                        }
                    }
                }
            }
        }
    }
    
    
    public function updateStuckQueueRecord()
    {
            $adminHours = 0;
        if (!$adminHours) {
            $adminHours = 2;
        }
          $collection = $this->create()->getCollection();
          $collection->addFieldToFilter('state', ['eq'=>$this->processingState]);
        if (count($collection)>0) {
            foreach ($collection as $item) {
                $date_a = $this->_date->date($item->getUpdatedAt());
                $date_b = $this->_date->date();
                $diff = $date_a->diff($date_b)->format('%i');
                if ($diff >= $adminHours) {
                    $item->setState($this->pendingState)->save();
                }
            }
        }
          return true;
    }

    public function checkQueueProcessingStatus()
    {
        $collection = $this->create()->getCollection();
        $collection->addFieldToFilter('state', [ 'eq'=>$this->processingState ]);
        if (count($collection)) {
            return true;
        } else {
            return false;
        }
    }
    
    public function cleanProcesQueue()
    {
        
        $collection = $this->create()->getCollection();
        $collection->addFieldToFilter('state', ['in' => [$this->completeState,$this->errorNotPaid]]);
        if (count($collection)>0) {
            foreach ($collection as $item) {
                $item->delete();
            }
        }

        //------------ clean already in send order report ------------
        $collection = $this->create()->getCollection();
        $collection->addFieldToFilter('state', ['in' => [ $this->pendingState, $this->errorState ]]);
        if (count($collection)>0) {
            foreach ($collection as $item) {
                 $data = $this->_salesorderreport->findRecord('mgt_order_id', $item->getIncrementId());
                if (count($data)>0) {
                    $item->delete();
                }
            }
        }
        
        //------------  remove not posted order from report and re added in queue again ------------
        $this->retryAddedFailedOrderFromReport();
    }



    public function retryAddedFailedOrderFromReport()
    {
    
        $serachTxt = 'You';
        $finalDate = date('Y-m-d H:i:s', strtotime('-15 minute'));
        $sorfCollection = $this->_salesorderreport->getCollection();
        // $sorfCollection->addFieldToFilter( 'bp_order_id',array('null' => true) );
        $sorfCollection->addFieldToFilter('bp_order_id', [ ['null' => true], ['like' => '%'.$serachTxt.'%'] ]);
        $sorfCollection->addFieldToFilter('update_at', ['lteq' => $finalDate]);
        // $sorfCollection->addFieldToFilter('update_at', ['gteq' => $finalDate]);
        if ($sorfCollection->getSize()) {
            foreach ($sorfCollection as $item) {
                $orderIncrementId = $item->getMgtOrderId();
                $order = $this->_objectManager->create('\Magento\Sales\Model\Order')->loadByIncrementId($orderIncrementId);
                if ($order->getId()) {
                    if ($searchResult = $this->findRecord('increment_id', trim($orderIncrementId))) {
                        $data = [];
                        $data['state'] =  $this->pendingState;
                        $this->updateRecord($searchResult->getId(), $data);
                    } else {
                        $data = [];
                        $data['order_id']         =  $order->getId();
                        $data['increment_id']     =  $order->getIncrementId();
                        $data['state']             =  $this->pendingState;
                        $this->addRecord($data);
                    }
                }
                $item->delete();
            }
        }
    }
}
