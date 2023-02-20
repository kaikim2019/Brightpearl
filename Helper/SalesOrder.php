<?php

namespace Bsitc\Brightpearl\Helper;

class SalesOrder extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_directoryList;
    protected $_scopeConfig;
    protected $_objectManager;
    protected $_storeManager;
    protected $_order;
    protected $attributeRepository;
    protected $attributeValues;
    protected $tableFactory;
    protected $_pricelist;
    protected $associatedproduct;
    protected $moduleManager;
    protected $_webhookinventory;
    protected $_api;
    protected $_logManager;
    protected $_webhookupdate;
    protected $productResourceModel;
    protected $productFactory;
    protected $_customerRepositoryInterface;
    protected $_countryFactory;
    protected $_salesorderreportFactory;
    protected $_orderqueue;
    protected $_bpshippingmapFactory;
    protected $_bpleadsource;
    protected $_bppaymentmapFactory;
    protected $_bptaxmapFactory;
    protected $_orderwarehousemap;
    protected $_bppricelistmap;
	

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Api\ProductAttributeRepositoryInterface $attributeRepository,
        \Magento\Eav\Model\Entity\Attribute\Source\TableFactory $tableFactory,
        \Magento\Sales\Model\Order $order,
        \Bsitc\Brightpearl\Model\PricelistFactory $pricelist,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Bsitc\Brightpearl\Model\AssociateproductFactory $associatedproduct,
        \Bsitc\Brightpearl\Model\OrderqueueFactory $orderqueue,
        \Bsitc\Brightpearl\Model\Api $api,
        \Bsitc\Brightpearl\Model\Logs $logManager,
        \Bsitc\Brightpearl\Model\SalesorderreportFactory $salesorderreportFactory,
        \Magento\Catalog\Model\ResourceModel\Product $productResourceModel,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Bsitc\Brightpearl\Model\BpshippingmapFactory $BpshippingmapFactory,
        \Bsitc\Brightpearl\Model\BpleadsourcemapFactory $bpleadsource,
        \Bsitc\Brightpearl\Model\BppaymentmapFactory $BppaymentmapFactory,
        \Bsitc\Brightpearl\Model\BptaxmapFactory $BptaxmapFactory,
        \Bsitc\Brightpearl\Model\OrderwarehousemapFactory $orderwarehousemap,
        \Bsitc\Brightpearl\Model\Bppricelistmap $bppricelistmap		
    ) {
        
        $this->_directoryList = $directoryList;
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_objectManager = $objectManager;
        $this->_storeManager = $storeManager;
        $this->attributeRepository  = $attributeRepository;
        $this->tableFactory = $tableFactory;
        $this->_pricelist = $pricelist;
        $this->_order = $order;
        $this->productFactory = $productFactory;
        $this->associatedproduct = $associatedproduct;
        $this->moduleManager = $moduleManager;
        $this->_logManager = $logManager;
        $this->_api = $api;
        $this->productResourceModel = $productResourceModel;
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        $this->_countryFactory = $countryFactory;
        $this->_salesorderreportFactory = $salesorderreportFactory;
        $this->_orderqueue = $orderqueue;
        $this->_bpshippingmapFactory = $BpshippingmapFactory;
        $this->_bpleadsource = $bpleadsource;
        $this->_bppaymentmapFactory = $BppaymentmapFactory;
        $this->_bptaxmapFactory = $BptaxmapFactory;
        $this->_orderwarehousemap = $orderwarehousemap;
        $this->_bppricelistmap = $bppricelistmap;		
        parent::__construct($context);
    }

    /**
     * Get store config
     */
    public function getConfig($path, $store = null)
    {
        return $this->_scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }
    
    public function getBrightpearl($store)
    {
        $apiObj = '';
        $bpConfigData  =  (array) $this->getConfig('bpconfiguration/api', $store);
        if (isset($bpConfigData['enable']) && isset($bpConfigData['bp_useremail']) && isset($bpConfigData['bp_password']) && isset($bpConfigData['bp_account_id']) && isset($bpConfigData['bp_dc_code']) && isset($bpConfigData['bp_api_version'])) {
            $apiObj = $this->_objectManager->create('\Bsitc\Brightpearl\Model\Api', ['data' => $bpConfigData]);
        }
        return $apiObj;
    }
    
    public function create(array $arguments = [])
    {
        return $this->_objectManager->create('Bsitc\Brightpearl\Model\Bpproducts', $arguments, false);
    }

    public function createpricelist(array $arguments = [])
    {
        return $this->_objectManager->create('Bsitc\Brightpearl\Model\Pricelist', $arguments, false);
    }

    /*Get configuration for Pricelist*/
    public function getPricelist()
    {
        return $pricelist  =  $this->getConfig('bpconfiguration/bp_orderconfig/order_price_list');
    }
    
    /*Get configuration for Channel*/
    public function getChannel()
    {
        return $pricelist  =  $this->getConfig('bpconfiguration/bp_orderconfig/mgt_order_channel');
    }

    /*Get configuration for POS Channel*/
    public function getPosChannel()
    {
        return $pricelist  =  $this->getConfig('bpconfiguration/bp_orderconfig/pos_order_channel');
    }

    /*Get configuration for Nominal Code*/
    public function getNominalCode()
    {
        return $pricelist  =  $this->getConfig('bpconfiguration/bp_orderconfig/order_product_nominal');
    }
    
    /*Get configuration for Shipping Nominal Code*/
    public function getShippingNominalCode()
    {
        return $pricelist  =  $this->getConfig('bpconfiguration/bp_orderconfig/order_shipping_nominal');
    }
    
    /*Get configuration for Discount Nominal Code*/
    public function getDiscountNominalCode()
    {
        return $pricelist  =  $this->getConfig('bpconfiguration/bp_orderconfig/order_discount_nominal');
    }
    
    /*Get configuration for store_credit_nominal Nominal Code*/
    public function getStoreCreditNominalCode()
    {
        return $pricelist  =  $this->getConfig('bpconfiguration/bp_orderconfig/store_credit_nominal');
    }

    /*Get configuration for rounding_sku_nominal Nominal Code*/
    public function getRoundingSkuNominalCode()
    {
        return $pricelist  =  $this->getConfig('bpconfiguration/bp_orderconfig/rounding_sku_nominal');
    }
    
    public function getRoundingThreshold()
    {
        return $pricelist  =  $this->getConfig('bpconfiguration/bp_orderconfig/rounding_threshold');
    }
    
    public function getRoundingEnable()
    {
        return $pricelist  =  $this->getConfig('bpconfiguration/bp_orderconfig/rounding_enable');
    }
    
    /*Get configuration for Adjustment Refunds Nominal Code*/
    public function getAdjustmentRefundsNominalCode()
    {
        return $pricelist  =  $this->getConfig('bpconfiguration/bp_sc_config/adjustment_refunds_nominal');
    }

    /*Get configuration for Order Status*/
    public function getMgtOrderStatus()
    {
        return $orderstatus  =  $this->getConfig('bpconfiguration/bp_orderconfig/mgt_order_status');
    }

    /*Get configuration for POS Order Status*/
    public function getPosOrderStatus()
    {
        return $posstatus  =  $this->getConfig('bpconfiguration/bp_orderconfig/pos_order_status');
    }

    /*Get configuration for POS Order Status*/
    public function getPreOrderStatus()
    {
        $pre_order_status  =  $this->getConfig('bpconfiguration/bp_orderconfig/pre_order_status');
        return $pre_order_status;
    }

    /*Get configuration for POS Order Status*/
    public function getConfigPosStaffName()
    {
        $posstaff  =  $this->getConfig('bpconfiguration/bp_orderconfig/posstaff');
        return $posstaff;
    }


    /*Get configuration for Channel*/
    public function getBpleadsource($customergroupid)
    {
        $enable  =  $this->getConfig('bpconfiguration/bp_orderconfig/enable');
        $customergroupid = trim($customergroupid);
        $mpleadsource = '';
        if ($enable) {
            $shippingcollections = $this->_bpleadsource->create()->getCollection();
            $shippingcollections = $shippingcollections->addFieldToFilter('code', $customergroupid);
            $shippingcollections = $shippingcollections->getData();
            if (count($shippingcollections)) {
                foreach ($shippingcollections as $shippingcollection) {
                    $mpleadsource = $shippingcollection['bpcode'];
                    break;
                }
            } else {
                $mpleadsource  =  $this->getConfig('bpconfiguration/bp_orderconfig/bpleadsource');
            }
        }
        return $mpleadsource;
    }

    
    public function getStoreIdFromLocationId($warehouse_id)
    {
        $data = '';
        /*
        $collection = $this->_objectManager->create('\Magestore\InventorySuccess\Model\WarehouseStoreViewMap')->getCollection();
        $collection->addFieldToFilter('warehouse_id', $warehouse_id);
        if ($collection->getSize()) {
            $data  = $collection->getFirstItem();
        }
        return $data;
        */
    }
    
    
    /*Get Order configuration for Pricelist*/
    public function getMappedPricelist($order)
    {
        $pricelist  =  $this->getConfig('bpconfiguration/bp_orderconfig/order_price_list');
        $storeid = $order->getStoreId();
        $pos_location_id = $order->getPosLocationId();
        if ($pos_location_id) {
             $wareHouseStoreViewMap = $this->getStoreIdFromLocationId($pos_location_id);
            if ($wareHouseStoreViewMap) {
                $storeid = $wareHouseStoreViewMap->getStoreId();
            }
        }
        $data = $this->_bppricelistmap->findRecord('store_id', $storeid);
        if ($data) {
            $pricelist = $data->getBpPrice();
        }
        return $pricelist ;
    }
    
    /*Get configuration for Warehouse*/
    /*First check first piority from order then from mapping then from selected store or pos*/
    public function getBpWarehouse($order)
    {
        /*If Order Placed from Magento frontend*/
        $warehouse = $order->getWarehouseStore();
        $storeid = $order->getStoreId();
        $pos = $order->getPosLocationId();
        if (!$warehouse) {
            $warehousecollections = $this->_orderwarehousemap->create()->getCollection();
            if ($pos) {
                $warehousecollections = $warehousecollections->addFieldToFilter('mgt_pos', $pos);
            } else {
                $warehousecollections = $warehousecollections->addFieldToFilter('mgt_store', $storeid);
            }
            $warehousecollections = $warehousecollections->getData();
            if (count($warehousecollections)) {
                foreach ($warehousecollections as $warehousecollection) {
                    $warehouse = $warehousecollection['bp_warehouse'];
                    break;
                }
            } else {
                $warehouse  =  $this->getConfig('bpconfiguration/bp_orderconfig/mgt_order_warehouse');
            }
        }
        return $warehouse;
    }
    
    /*Get configuration for Shipping Methods*/
    public function getShippingMapping($mgt_shippingmethod)
    {
        $enable  =  $this->getConfig('bpconfiguration/bp_orderconfig/enable');
        $shippingmethod = trim($mgt_shippingmethod);
        $mpsshippingmethods = '';
        if ($enable) {
            $shippingcollections = $this->_bpshippingmapFactory->create()->getCollection();
            $shippingcollections = $shippingcollections->addFieldToFilter('code', ['like' => '%'.$shippingmethod.'%']);
            $shippingcollections = $shippingcollections->getData();
            if (count($shippingcollections)) {
                foreach ($shippingcollections as $shippingcollection) {
                    $mpsshippingmethods = $shippingcollection['bpcode'];
                    break;
                }
            } else {
                    $mpsshippingmethods  =  $this->getConfig('bpconfiguration/bp_orderconfig/bpshippingmethod');
            }
        }
        return $mpsshippingmethods;
    }
    
    /*Get configuration for Shipping Methods*/
    public function getMapPaymentMethod($paymentcode)
    {
        $enable  =  $this->getConfig('bpconfiguration/bp_orderconfig/enable');
        $paymentcode = trim($paymentcode);
        $mpnominalcode = '';
        if ($enable) {
            $paymentcollections = $this->_bppaymentmapFactory->create()->getCollection();
            $paymentcollections = $paymentcollections->addFieldToFilter('code', $paymentcode);
            $paymentcollections = $paymentcollections->getData();

            if (count($paymentcollections)) {
                foreach ($paymentcollections as $paymentcollection) {
                    $mpnominalcode = $paymentcollection['bpcode'];
                    break;
                }
            } else {
                    $mpnominalcode  =  $this->getConfig('bpconfiguration/bp_orderconfig/order_product_nominal');
            }
        }
        return $mpnominalcode;
    }

    /*Get configuration for Shipping Methods*/
    public function getTaxable($taxclassid)
    {
        $enable  =  $this->getConfig('bpconfiguration/bp_orderconfig/enable');
        $taxablecode = '';
        $taxclassid = $taxclassid;
        if ($enable) {
            $taxcollections = $this->_bptaxmapFactory->create()->getCollection();
            $taxcollections = $taxcollections->addFieldToFilter('code', $taxclassid);
            $taxcollections = $taxcollections->getData();
            if (count($taxcollections)) {
                foreach ($taxcollections as $taxcollection) {
                    $taxablecode = $taxcollection['bpcode'];
                    break;
                }
            } else {
                    $taxablecode  =  $this->getConfig('bpconfiguration/bp_orderconfig/taxcode');
            }
        }
        return $taxablecode;
    }

    public function getNonTaxable()
    {
        $enable  =  $this->getConfig('bpconfiguration/bp_orderconfig/enable');
        $taxablecode = '';
        if ($enable) {
                    $taxablecode  =  $this->getConfig('bpconfiguration/bp_orderconfig/notaxcode');
        }
        return $taxablecode;
    }
    
    /*Insert Data in log tables*/
    public function recordLog($cat, $log_data, $title)
    {
        $logArray = [];
        if (!$cat) {
            $cat = "Global";
        }
         $logArray['category'] = $cat;
         $logArray['title'] =  $title;
         $logArray['store_id'] =  0;
         $logArray['error'] =  json_encode($log_data, true);
         $this->_logManager->addLog($logArray);
         return true;
    }
     
     
     /*Insert Data in Sales Order Report tables*/
    public function setFirstSalesReportData($coloumn, $value)
    {
        $updatesaleslogs = $this->_salesorderreportFactory->create();
        $updatesaleslog = $updatesaleslogs->getCollection();
        $updatesaleslog = $updatesaleslog->addFieldToFilter($coloumn, $value);
        $updatesaleslog = $updatesaleslog->getData();
        $updateid = '';
        if (empty($updatesaleslog)) {
            $updatemgtid = $updatesaleslogs->setData($coloumn, $value);
            $savedata = $updatemgtid->save();
            $updateid = $updatemgtid->getId();
        } else {
            foreach ($updatesaleslog as $updatesales) {
                $updateid = $updatesales['id'];
                break;
            }
        }
        return $updateid;
    }
    
     /*Update Sales Order Report tables*/
    public function updateSalesReportData($id, $coloumn, $value)
    {
           $updatesaleslog = $this->_salesorderreportFactory->create();
           $updateid = '';
           $updatesaleslog = $updatesaleslog->load($id);
           $updatesaleslog = $updatesaleslog->setData($coloumn, $value);
           $updatesaleslog = $updatesaleslog->save();
           $updateid = $updatesaleslog->getId();
           return $updateid;
    }

     /*Check and Update Sales Order Report tables*/
    public function ChecklogData($id, $coloumn)
    {
        $updatesaleslogs = $this->_salesorderreportFactory->create();
        $updatesaleslog = $updatesaleslogs->getCollection();
        $updatesaleslog = $updatesaleslog->addFieldToFilter('id', $id);
        $updatesaleslog = $updatesaleslog->getData();
        $value = '';
        foreach ($updatesaleslog as $updatesales) {
            $custid = $updatesales['bp_customer_id'];
            if ($custid) {
                $value = 'true';
            } else {
                $value = 'false';
            }
        }
        return $value;
    }

    
    /*Send Order from MGT to BP*/
    public function CreateBpSalesOrder()
    {
        /*Fetch Pending status from Order Queue table*/
        $collections = $this->_orderqueue->create()->getCollection();
        $collections = $collections->addFieldToFilter('state', 'pending');
        foreach ($collections as $collection) {
            $orderId = $collection->getOrderId();
            $incrementId = $collection->getIncrementId();
            if ($orderId) {
                $id = $collection->getId();
                $queueorder = $collection->load($id);
                $data = $queueorder->setState("processing");
                $queueorder->save();
                /*Call a BP customer functions and set shipping and billing address initially*/
                $response = $this->CreateOrder($orderId, $incrementId);
                if ($response) {
                    $data = $queueorder->setState("complete");
                    $queueorder->save();
                } else {
                    $data = $queueorder->setState("error");
                    $queueorder->save();
                }
            }
        }
    }

    /*Create Customer and Address in BP*/
    public function CreateOrder($orderId, $incrementId)
    {
        $order = $this->_objectManager->create('\Magento\Sales\Model\Order')->load($orderId);
        $billingAdress = $order->getBillingAddress()->getData();
        $shippingAdress = $order->getShippingAddress()->getData();
        /*Create log for reports*/
        $salesreportid = '';
        if ($orderId) {
            $salesreportid = $this->setFirstSalesReportData('mgt_order_id', $incrementId);
        } else {
            $error = "Order Id does not exits.";
            $log_data = json_encode($error);
            $this->recordLog($cat = "Order", $log_data, $title = "Order not exits");
        }
        /*If Not Shipping Address set billing as shipping address*/
        if (!$shippingAdress) {
            $shippingAdress = $order->getBillingAddress()->getData();
            $error = "There is no shipping address with this order id = ".$incrementId;
            $log_data = json_encode($error);
            $this->recordLog($cat = "Order", $log_data, $title = $incrementId);
        }
        /*Guest Customer*/
        if (!$order->getCustomerId()) {
            $customerInfo = $this->getBpCustomerInfoArray($billingAdress, $telephone = "");
            $email = $billingAdress['email'];

            /*For Guest Customer Logs*/
            if ($salesreportid) {
                $this->updateSalesReportData($salesreportid, 'mgt_customer_id', $value = 0);
            }
        } else {
            /*Register Customer*/
            $customerId = $order->getCustomerId();
            //$customer = $this->_customerRepositoryInterface->getById($customerid);
             $customerFactory = $this->_objectManager->get('\Magento\Customer\Model\CustomerFactory')->create();
            $customer = $customerFactory->load($customerId);
            $customer = $customer->getData();
            $telephone = '';
            if (array_key_exists('telephone', $billingAdress)) {
                $telephone = $billingAdress['telephone'];
            }
            $customerInfo = $this->getBpCustomerInfoArray($customer, $telephone);
            $customerInfo['financialDetails']['priceListId'] = $this->getMappedPricelist($order);
            $email = $customer['email'];
            /*For Register Customer Logs*/
            if ($salesreportid) {
                $this->updateSalesReportData($salesreportid, 'mgt_customer_id', $customerId);
            }
        }
        $Bil_Address = $this->getAddressArray($billingAdress, $order->getBillingAddress());
        $Ship_Address = $this->getAddressArray($shippingAdress, $order->getShippingAddress());
        if ($this->_api->authorisationToken) {
            /*Search Customer are alreday exits*/
            $res = $this->_api->searchCustomerByEmail($email, $return_type = "object") ;
            $result = [];
            $resArray =  json_decode(json_encode($res), true);
            if (array_key_exists('response', $resArray)) {
                $responseArray = $resArray['response'];
                if (array_key_exists('results', $responseArray)) {
                    $result = $res->response->results;
                }
            }
            $log_data = "Check Customer Exits".json_encode($res);
            $this->recordLog($cat = "Order", $log_data, $title = $incrementId);
            usleep(1000000);
            $flagCustomerExist = false;
            $brightpearlUserId = '';
            if (count($result)) {
                foreach ($result as $_result) {
                    $response = $this->_api->getCustomerById($_result[0]);
                    $res = $response['response'];
                    /*Check error for customer exits at BP*/
                    if (isset($res['errors'])) {
                        $log_data = "get customer from bp".json_encode($res['errors']);
                        $this->recordLog($cat = "Order", $log_data, $title = $incrementId);
                    } else {
                        $log_data = "get customer from bp".json_encode($res);
                        $this->recordLog($cat = "Order", $log_data, $title = $incrementId);
                    }
                    if (strpos(json_encode($res), 'isSupplier') > 0 && strpos(json_encode($res), 'isStaff') > 0) {
                        if ($res[0]['relationshipToAccount']['isStaff'] != 1 && $res[0]['relationshipToAccount']['isSupplier'] != 1) {
                            $flagCustomerExist = true;
                            $brightpearlUserId = $res[0]['contactId'];
                            /*Log tables*/
                            $log_data = "BP customer already exits with id".json_encode($brightpearlUserId);
                            $this->recordLog($cat = "Order", $log_data, $title = $incrementId);
                            break;
                        }
                    }
                }
            }
            /*Customer does not exits*/
            if ($flagCustomerExist == false) {
                /*POST Customer Address First*/
                /*POST Customer address in Log tables*/
                //$log_data = "Customer address request".json_encode($Bil_Address);
                //$this->recordLog($cat = "Order", $log_data, $title=$incrementId);
                $billId = '';
                $response     = $this->_api->postCustomerAddress($Bil_Address);
                if (array_key_exists('response', $response)) {
                    $billId = $response['response'];
                }
                /* ---------- add retry code if not received response --------*/
                if (!$billId && $billId == "") {
                    $response     = $this->_api->postCustomerAddress($Bil_Address);
                    if (array_key_exists('response', $response)) {
                        $billId = $response['response'];
                    }
                }
                /* ---------- add retry code if not received response --------*/
                /*Error log for customer Address*/
                $log_data = "Customer address response ".json_encode($response);
                $this->recordLog("Order", $log_data, $incrementId);
                /*Check if billing and shipping address are same*/
                $delId = '';
                if (array_diff($Bil_Address, $Ship_Address)) {
                    $response = $this->_api->postCustomerAddress($Ship_Address);
                    if (array_key_exists('response', $response)) {
                        $delId = $response['response'];
                    }
                    /* ---------- add retry code if not received response --------*/
                    if (!$delId && $delId == "") {
                        $response = $this->_api->postCustomerAddress($Ship_Address);
                        if (array_key_exists('response', $response)) {
                            $delId = $response['response'];
                        }
                    }
                    /* ---------- add retry code if not received response --------*/
                } else {
                    $delId = $billId;
                }
                /*Log for Customer bill id*/
                if (!$billId && $billId == "") {
                    $log_data = ' Add Billing API Call Fail ';
                    $this->recordLog($cat = "Order", $log_data, $title = $incrementId);
                } else {
                    $log_data = 'BP Billing address id '.$billId;
                    $this->recordLog($cat = "Order", $log_data, $title = $incrementId);
                }
				/* Update address id in customer info array */
				$customerInfo['postAddressIds'] = [
					'DEF' => $billId,
					'BIL' => $billId,
					'DEL' => $delId
				];
					

				/*POST Customer to brightpearl*/
				$log_data = "Post customer to BP request ".json_encode($customerInfo);
				$this->recordLog($cat = "Order", $log_data, $title = $incrementId);
													
				/*Post Customer to Bright Pearls*/
				$CreateUserResponse = $this->_api->postCustomer($customerInfo);
				if (array_key_exists('response', $CreateUserResponse)) {
					$brightpearlUserId = $CreateUserResponse['response'];

						$log_data = "Post customer to BP response ".json_encode($CreateUserResponse);
						$this->recordLog($cat = "Order", $log_data, $title = $incrementId);
				} else {
				/*Log get Customer BP ID*/
					$log_data = "Customer BP id ".json_encode($CreateUserResponse['errors']);
					$this->recordLog($cat = "Order", $log_data, $title = $incrementId);
				}
            }
                        
			/*Set Customer for BP IN logs*/
            if ($salesreportid) {
                $brightpearluser = $this->updateSalesReportData($salesreportid, 'bp_customer_id', $brightpearlUserId);
            }

			/*Log table*/
			$bpcustlog = $this->ChecklogData($salesreportid, 'bp_customer_id');
            if ($bpcustlog == 'true') {
                $brightpearluser = $this->updateSalesReportData($salesreportid, 'bp_customer_status', $value = "success");
            } else {
                $brightpearluser = $this->updateSalesReportData($salesreportid, 'bp_customer_status', $value = "error");
            }
                        
			/*Send data to create Order*/
			$bporderId = $this->getBpOrderData($order, $brightpearlUserId, $email, $shippingAdress);
                        
            if ($bporderId) {
                $log_data = "BP Order id ".json_encode($bporderId);
                $this->recordLog($cat = "Order", $log_data, $title = $incrementId);
            } else {
                $log_data = "BP Order id does not exits.";
                $this->recordLog($cat = "Order", $log_data, $title = $incrementId);
            }
                        
			/*Set Customer for BP IN logs*/
            if ($salesreportid) {
                $brightpearluser = $this->updateSalesReportData($salesreportid, 'bp_order_id', $bporderId);

                /*Update Order Comments in Magento*/
                /*$Obj_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $custom_order = $Obj_objectManager->create('\Magento\Sales\Model\Order')->load($orderId);*/

                $custom_order =  $this->_order->load($orderId);
                $custom_order->addStatusHistoryComment('This Brightpearl Order id - '.$bporderId);
                $custom_order->save();
            }
                        
			$bporderstatus = $this->ChecklogData($salesreportid, 'bp_order_status');

            if ($bporderstatus == 'true') {
                $brightpearluser = $this->updateSalesReportData($salesreportid, 'bp_order_status', $value = "success");
            } else {
                $brightpearluser = $this->updateSalesReportData($salesreportid, 'bp_order_status', $value = "error");
            }
                        
                        
			/* Set Rows in Orders */
			$data = $this->getOrderRow($order, $bporderId);

            if ($data) {
                $log_data = "BP Order row data ".json_encode($data);
                $this->recordLog($cat = "Order", $log_data, $title = $incrementId);
            } else {
                $log_data = "BP Order Row data API Response";
                $this->recordLog($cat = "Order", $log_data, $title = $incrementId);
            }
			usleep(1000000);

			/* Set Inventory to Order Rows */
            if ($data) {
                    $salesOrderRowId = $data['salesOrderRowId'];
                    $reservationProductArray = $data['reservationProductArray'];
                    $res = $this->setInventoryToOrderRow($order, $incrementId, $bporderId, $salesOrderRowId, $reservationProductArray);
                if (!isset($res->errors)) {
                        $this->updateSalesReportData($salesreportid, 'bp_inventory_status', $value = "success");
                } else {
                        $this->updateSalesReportData($salesreportid, 'bp_inventory_status', $value = "error");
                }
            }

			/* ----- Set Discount amount with Coupon code ----- */
			$this->postOrderComment($order, $bporderId);

			/* ----- Set Discount amount with Coupon code ----- */
			$this->CouponCodeDiscount($order, $bporderId);

			/* ----- Order Shipping charges ----- */
			$this->OrderShippingCharge($order, $bporderId);

			/* ----- Reduce Store Credit / Customer Balance Amount from Order ----- */
			// $this->CustomerBalanceAmount($order, $bporderId);

			/* ----- add rounding fix row  Order ----- */
			$this->addRoundingDifferencAmountRow($order, $bporderId);

			/* ----- POS Staff Name ----- */
			$posid = $order->getPosStaffId();
			
            if ($posid) {
                $this->postPosStaffName($order, $bporderId);
            }
			usleep(1000000);
                        
			/*Check If Order are paid and has Invoiced then send payment to Brightpearl*/
			$bpPaymentPaid  = '';
            if (($order->getBaseTotalDue() == 0) && ($order->getInvoiceCollection()->getSize() > 0)) {
                $Bpapi = $this->_api;
                $response_data = $this->postCustomerPayment($Bpapi, $order, $bporderId, $brightpearlUserId);
                $paymentassign = json_decode($response_data);

                $bpPaymentPaid = $paymentassign->response;
                            
                if ($paymentassign->response) {
                    $log_data = "Order Payment Response ".json_encode($paymentassign->response);
                    $this->recordLog($cat = "Order", $log_data, $title = $incrementId);
                    $this->updateSalesReportData($salesreportid, 'bp_payment_status', $value = "success");
                } else {
                    $log_data = "Order Payment Response ".json_encode($paymentassign);
                    $this->recordLog($cat = "Order", $log_data, $title = $incrementId);
                    $this->updateSalesReportData($salesreportid, 'bp_payment_status', $value = "error");
                }
            } else {
                $bpPaymentPaid  = 'notpaid';
                $log_data = "Order Payment Response - payment are not paid yet";
                $this->recordLog($cat = "Order", $log_data, $title = $incrementId);
                $this->updateSalesReportData($salesreportid, 'bp_payment_status', $value = "not paid");
            }
                        
                        /* --------- post store credit or customer balance as  a payment ---------------*/
            if ($order->getCustomerBalanceAmount() > 0) {
                $Bpapi = $this->_api;
                $response_data = $this->postStoreCreditPayment($Bpapi, $order, $bporderId, $brightpearlUserId);
                $paymentassign = json_decode($response_data);

                $bpPaymentPaid = $paymentassign->response;
                            
                if ($paymentassign->response) {
                    $log_data = "Order store credit Payment Response ".json_encode($paymentassign->response);
                    $this->recordLog("Order", $log_data, $incrementId);
                    $this->updateSalesReportData($salesreportid, 'bp_payment_status', $value = "success");
                } else {
                     $log_data = "Order store credit Payment Response ".json_encode($paymentassign);
                    $this->recordLog("Order", $log_data, $incrementId);
                    $this->updateSalesReportData($salesreportid, 'bp_payment_status', $value = "error-Store-credit");
                }
            }
                        /* --------- post store credit or customer balance as  a payment ---------------*/
                        
                        /* --------- post  Gift Cards Payment as a payment ---------------*/
            if ($order->getGiftCardsAmount() > 0) {
                 $Bpapi = $this->_api;
                $response_data = $this->postGiftCardsPayment($Bpapi, $order, $bporderId, $brightpearlUserId);
                $paymentassign = json_decode($response_data);

                $bpPaymentPaid = $paymentassign->response;
                            
                if ($paymentassign->response) {
                    $log_data = "Order Gift Cards Payment Response ".json_encode($paymentassign->response);
                    $this->recordLog("Order", $log_data, $incrementId);
                    $this->updateSalesReportData($salesreportid, 'bp_payment_status', $value = "success");
                } else {
                     $log_data = "Order Gift Cards Payment Response ".json_encode($paymentassign);
                    $this->recordLog("Order", $log_data, $incrementId);
                    $this->updateSalesReportData($salesreportid, 'bp_payment_status', $value = "error-Store-credit");
                }
            }
			/* --------- post  Gift Cards Payment as a payment  ---------------*/
			
			// ------------ POST ORDER CUSTOM ATTRIBUTE --------------------
			$this->postOrderCustomAttributes($order, $bporderId);

        } else {
                /*BP API Authentication failed*/
                $log_data = "API Authentication fails";
                $this->recordLog($cat = "Order", $log_data, $title = $incrementId);
        }

                    /*Check If Order are POS order then generate Fullfillments*/
                    //$goodsoutnote  = $this->PostGoodsOutNote($Bpapi, $order, $bporderId, $brightpearlUserId);
                    //$paymentassign = json_decode($response_data);

                    $pos                 = $order->getPosLocationId();
                    $shippingmethod     = $order->getShippingMethod();
                    $shippingMethodId   = $this->getShippingMapping($shippingmethod);
                    $collectfrom_store = $order->getWarehouseStore();

                    /*Assign error msg for status*/
                    $successresp = $bpPaymentPaid;

        if (($pos) || ($collectfrom_store)) {
            // ---------- skip pos order if order in processing state due to home delivery in pos
            if (($pos) && ($order->getState() == 'processing')) {
                return $successresp;
            }
                    
            if ($data and count($data['reservationProductArray']) >0) {
                $products = $data['reservationProductArray'];
                  $prodata = '[ '.implode(",", $products).' ]';
                 $warehouseid = $this->getBpWarehouse($order);
                $goodsdata = '{"warehouses": [{"releaseDate": '.date("Y-m-d").',"warehouseId": '.$warehouseid.',"transfer": false,"products": '.$prodata.'}],"priority": false,"shippingMethodId": '.$shippingMethodId.',"labelUri": ""}';

                $this->recordLog("Order", 'Order Goods Out note POST Data'.$goodsdata, $incrementId);
                            
                 $goodsoutnote  = $this->_api->PostGoodsOutNote($bporderId, $goodsdata);
                             
                $log_data = "Order Goods Out note Response ".$goodsoutnote;
                $this->recordLog("Order", $log_data, $incrementId);
                $successresp = json_decode($goodsoutnote, true);
                if (array_key_exists("response", $successresp)) {
                            $response = $successresp['response'];
                             $this->postShipmentEvent($response, $incrementId);
                }
            }
        } else {
            $successresp = $bpPaymentPaid;
        }
         return $successresp;
    }


     /*
    * Post POS Staff Name
    */
    public function postPosStaffName($order, $bporderId)
    {
                        /*Mick*/
                        $posstaffname = $order->getPosStaffName();
        if ($posstaffname) {
            $path = $this->getConfigPosStaffName();
        /*$data = '[
                                    {
                                    "op":      "add",
                                    "path":  "/'.$path.'",
                                    "value": "'.$posstaffname.'"
                                    }
                ]';*/
            $data = [['op'=> 'add', 'path' => '/'.$path, 'value' => $posstaffname]];
            $response = $this->_api->postOrderCustomAttribute($bporderId, $data);
            $log_data = "Post POS Staff Name ".json_encode($data);
            $this->recordLog($cat = "POS Staff Logs", $log_data, '');
        }
    }


     /*
    * Post Shipment Event
    */
    
    public function postShipmentEvent($response, $incrementId)
    {
        if (count($response) > 0) {
            foreach ($response as $goodnoteID) {
                $result        = $this->_api->getGoodsOutNote($goodnoteID) ; // get good out note
                if (array_key_exists("response", $result)) {
                    $gonData         = $result['response'][$goodnoteID];
                    $eventOwnerId     = $gonData['createdBy'];
                    
                    $tmp = [];
                    $tmp['eventCode']         = 'SHW';
                    $tmp['occured']         = date("c", time());
                    $tmp['eventOwnerId']     = $eventOwnerId;
                    
                    $eventData = [];
                    $eventData['events'][] = $tmp;
                    $log_data = "Post Shipment Event Data ".json_encode($eventData);
                    $this->recordLog($cat = "Shipment Event", $log_data, '');

                    $response = $this->_api->postShipmentEvent($goodnoteID, $eventData);
                    $log_data = "Post Shipment Event Response ".json_encode($response);
                    $this->recordLog($cat = "Shipment Event", $log_data, '');
                }
            }
        }
    }
    
 
    /*Order Data for Delivery date*/
    public function getOrderDeliveryDate($order, $delday, $type = 'pre_order')
    {
        
          $deliverydate = '';
          $oprObj = $this->_objectManager->create('Bsitc\Brightpearl\Model\Bporderporelation');
         $collection = $oprObj->getCollection()->addFieldToFilter('order_id', $order->getId())->setOrder('deliverydate', 'desc');
        if ($collection->getSize()) {
            $item =  $collection->getFirstItem();
            $deliverydate  = $item->getDeliverydate();
        } else {
            $deliverydate = date('Y-m-d h:m:s', strtotime($order->getCreatedAt() . '+' . $delday . 'days'));
        }
        if ($type =='parent') {
            $deliverydate = date('Y-m-d h:m:s', strtotime($order->getCreatedAt() . '+' . $delday . 'days'));
            return $deliverydate;
        }
        return $deliverydate;
    }
 
    

    /*Create Order and Pass the data in Orders*/
    public function getBpOrderData($order, $brightpearlUserId, $email, $shippingAdress)
    {

                /*Magento Shipping Methods*/
                $shippingmethod = $order->getShippingMethod();
                
                /*Fetch Shipping Method from mapping or config*/
                $shippingMethodId = $this->getShippingMapping($shippingmethod);

                /*Magento Customer Group Id*/
                $customergroupid = $order->getCustomerGroupId();
                
                /*Fetch Lead Source from mapping or config*/
                $leadsourceid = $this->getBpleadsource($customergroupid);
                
                //$shippingMethodId = 1; # set default id
                $delday = 1;  # set default id

                $warehouseid = $this->getBpWarehouse($order);
                $channelId     = $this->getChannel();
                
                /*Check If order types for Magento or POS*/
                $pos = $order->getPosLocationId();
        if ($pos) {
            $order_status = $this->getPosOrderStatus();
            $channelId     = $this->getPosChannel();
            /*Set POS Warehouse*/
           //$warehouseid = $this->getPosWarehouse($pos);
        } elseif ($order->getOrderType() == 1) {
            $order_status = $this->getPreOrderStatus();
        } else {
            $order_status = $this->getMgtOrderStatus();
        }

                /*Get Mapping for shipping methods and Data*/
                $reference             = $order->getIncrementId();
                 $placedOn             = date(DATE_ISO8601, strtotime($order->getCreatedAt()));
                $deldate             = $this->getOrderDeliveryDate($order, $delday, 'pre_order');
                 $deliveryDate        = date(DATE_ISO8601, strtotime($deldate));
                $currency             = $order->getOrderCurrencyCode();
                $priceListId        = $this->getMappedPricelist($order);
                 $addressFullName     = $shippingAdress['firstname'] . ' ' . $shippingAdress['lastname'];
                $companyName         = $shippingAdress['company'];
                $telephone             = $shippingAdress['telephone'];
                $mobileTelephone     = '';
                $orderStatusId         = $order_status;
                 //  -------- prepare array to post data on BP -----------
                $step1 = [];
                $step1['orderTypeCode'] = 'SO';
                $step1['reference'] = $reference;
                $step1['priceListId'] = $priceListId;
                $step1['placedOn'] = $placedOn;
                $step1['orderStatus']['orderStatusId'] = $orderStatusId;
                $step1['delivery']['deliveryDate'] = $deliveryDate;
                $step1['delivery']['shippingMethodId'] = $shippingMethodId;
                $step1['currency']['orderCurrencyCode'] = $currency;
                $step1['currency']['fixedExchangeRate'] = 'true';
                //$step1['currency']['exchangeRate'] = $this->getMgtExchangeRate($order);
                //$step1['currency']['exchangeRate'] = '';
                $step1['assignment']['current']['channelId'] = $channelId;
                $step1['parties']['customer']['contactId'] = $brightpearlUserId;
                $step1['parties']['delivery']['addressFullName'] = $addressFullName;
                $step1['parties']['delivery']['companyName'] = $companyName;
                $step1['parties']['delivery'] = $this->getAddressArray($shippingAdress, $order->getShippingAddress());
                $step1['parties']['delivery']['telephone'] = $telephone;
                $step1['parties']['delivery']['mobileTelephone'] = $mobileTelephone;
                $step1['parties']['delivery']['email'] = $email;
                $step1['parties']['delivery']['addressFullName'] = $addressFullName;
                $step1['parties']['delivery']['companyName'] = $companyName;

                /*Advance data*/
                $step1['assignment']['current']['leadSourceId'] = $leadsourceid;
                
                //$step1['warehouseId'] = $this->getBpWarehouse();

                $step1['warehouseId'] = $warehouseid;
                
                
                /* ------------  custom filed --------------------*/
                //$data = [['op'=> 'add', 'path' => '/PCF_POSSTAFF', 'value' => 'VASVIJAY']];
    
                //$step1['customFields'][]['PCF_POSSTAFF'] = 'VASVIJAY';
                //$this->recordLog("Order", json_encode($step1), 'Order Data');
                
                /* ------------  custom filed --------------------*/
                
                

                 /*if($this->warehouseId > 0) {
                     $step1['warehouseId'] = $this->warehouseId;
                }*/

                /*Post Order Data to logs*/
                $log_data = json_encode($step1);
                $this->recordLog($cat = "Order Data", $log_data, $title = $reference);

                 $response = $this->_api->postOrder($step1);
                $orderId = '';
        if (array_key_exists('errors', $response)) {
            /*Logging Data*/
            $log_data = json_encode($response);
            $this->recordLog($cat = "Order", $log_data, $title = $reference);
        } else {
            $orderId = $response['response'];
        }
                return $orderId;
    }


    /* ---- Add Row to Orders(Add product to Order) ------ */
    public function getOrderRow($order, $orderId)
    {
        $data = [];
        $items = $order->getAllVisibleItems();
        $reservationProductArray = [];
        $reference = $order->getIncrementId();
        foreach ($items as $itemId => $item) {
              /*Get Product Id*/
              $productid = $item['product_id'];
              $productRepository = $this->_objectManager->get('\Magento\Catalog\Model\ProductRepository');
              $product = $productRepository->getById($productid);
              $taxclassid = $product->getTaxClassId();
              $final_sku = $item->getSku();
              $final_sku =  str_replace("&", "%26", $final_sku);
			  $final_sku =  str_replace(" / ", "+%2F+", $final_sku);
              $pid = $this->_api->getProductIDFromSku($final_sku);               
              $discount_tax_compensation_amount =  0;
            if ($item->getDiscountTaxCompensationAmount()  > 0) {
                $discount_tax_compensation_amount = $item->getDiscountTaxCompensationAmount();
            }
                 
                
            if ($pid) {
                $productId = (int) $pid;
                $nominalcode = '';
                $taxCode = '';
                $rowTax = '';
                    
               /* ---------- get product data from brightpearl ---------*/
                $bpProduct = [];
                $bpProductData = $this->_api->getProductById($productId);
                if (array_key_exists('response', $bpProductData)) {
                    if (array_key_exists('0', $bpProductData['response'])) {
                        $bpProduct = $bpProductData['response'][0];
                    }
                }
                 /* ---------- get product data from brightpearl ---------*/
                if (array_key_exists('nominalCodeSales', $bpProduct) and $bpProduct['nominalCodeSales']!= "") {
                     $nominalcode = $bpProduct['nominalCodeSales'];
                } else {
                    $nominalcode = $this->getNominalCode();
                }
                    
                if ($item->getTaxAmount()) {
                    $taxCode = $this->getTaxable($taxclassid); #  T For Taxable
                    $rowTax = number_format($item->getTaxAmount(), '2', '.', '') ;
                } else {
                    $taxCode = $this->getNonTaxable();  # N for Non Taxable
                    $rowTax = '0.00';
                }
                    
                  $rowTax = $rowTax + $discount_tax_compensation_amount;
                    
                  $rowTotal = number_format($item->getRowTotal(), '2', '.', '');
                  $quantity = (int)$item->getQtyOrdered();
                  $prow                                 = [];
                  $prow['productId']                     = $productId;
                  $prow['quantity']['magnitude']         = $quantity;
                  $prow['rowValue']['taxCode']         = $taxCode;
                  $prow['rowValue']['rowNet']['value'] = $rowTotal;
                  $prow['rowValue']['rowTax']['value'] = $rowTax;
                  $prow['nominalCode']                  = $nominalcode;

                  $log_data = 'Post Order Data request : ' . json_encode($prow);
                  $this->recordLog($cat = "Order", $log_data, $title = $reference);  /*  ---- Request logs ---- */
                  $responses = $this->_api->postOrderRow($orderId, $prow);
                  $salesOrderRowId = '';
                if (array_key_exists('response', $responses)) {
                    $salesOrderRowId = $responses['response'];
                }
                    
                if (preg_match("/\bmany requests\b/i", $salesOrderRowId) || preg_match("/\bYou have sent too many requests\b/i", $salesOrderRowId)) {
                    usleep(1000000);
                    $responses = $this->_api->postOrderRow($orderId, $prow);
                    $salesOrderRowId = '';
                    if (array_key_exists('response', $responses)) {
                        $salesOrderRowId = $responses['response'];
                    }
                }
                     
                  /*Start Assigning data to arrays*/

                  /*Response logs*/
                  $log_data = 'Post Order Data response : ' . json_encode($responses);
                  $this->recordLog($cat = "Order", $log_data, $title = $reference);


                  $data['salesOrderRowId'] = $salesOrderRowId;
                  /*Ends Assigning data to arrays*/

                if ($salesOrderRowId > 0) {
                    # ---- Prepare array  for inventory reservation --------
                    $reservationProductArray[] = '{productId:"' . $productId . '",salesOrderRowId:"' . $salesOrderRowId . '",quantity:"' . $quantity . '"}';
                }
                  $data['reservationProductArray'] = $reservationProductArray;
            } else {
                /*!!!! If products does not exits !!!!*/
                 //  ---- start add missing sku in row ----
                $taxCode = '';
                $rowTax = '';
                if ($item->getTaxAmount()) {
                      $taxCode = $this->getTaxable($taxclassid); #  T For Taxable
                      $rowTax = number_format($item->getTaxAmount(), '2', '.', '') ;
                } else {
                    $taxCode = $this->getNonTaxable();  # N for Non Taxable
                    $rowTax = '0.00';
                }
                    
                   $rowTax = $rowTax + $discount_tax_compensation_amount;

                   $nominalcode = $this->getNominalCode();
                   $rowTotal     = number_format($item->getRowTotal(), '2', '.', '');
                   $quantity     = (int)$item->getQtyOrdered();
                   $prow = [];
                   $prow['productName'] = 'Missing SKU : ' . $final_sku . 'Missing Product : ' . $item->getProduct()->getName();
                   $prow['quantity']['magnitude'] = $quantity;
                   $prow['rowValue']['taxCode'] = $taxCode;
                   $prow['rowValue']['rowNet']['value'] = $rowTotal;
                   $prow['rowValue']['rowTax']['value'] = $rowTax;
                   $prow['nominalCode']                  = $nominalcode;
                   $response = $this->_api->postOrderRow($orderId, $prow);
                 $salesOrderRowId = '';
                    
                if (array_key_exists('response', $response)) {
                    $salesOrderRowId = $response['response'];
                    if (preg_match("/\bmany requests\b/i", $salesOrderRowId) || preg_match("/\bYou have sent too many requests\b/i", $salesOrderRowId)) {
                           usleep(1000000);
                           $response = $this->_api->postOrderRow($orderId, $prow);
                           $salesOrderRowId = $response['response'];
                    }
                } else {
                    usleep(1000000);
                    $response = $this->_api->postOrderRow($orderId, $prow);
                    if (array_key_exists('response', $response)) {
                        $salesOrderRowId = $response['response'];
                    }
                }
                     
                 $log_data = 'Response from missing sku : ' . json_encode($response);
                 $this->recordLog($cat = "Order", $log_data, $title = $reference);
                 //  ---- start add missing sku in row ----
            }
        }
		
		
		
            return $data;
    }
	
	/* -------------------- post order comment ----------------------------------------- */
    public function postOrderComment($order, $bpOrderId)
    {
       if( $order->getUmOrderComment() )
	   {
 			$postRow = [];
			$postRow['productName']						= 'Comment:'.$order->getUmOrderComment();
			$postRow['quantity']['magnitude']			= 1;
			$postRow['rowValue']['taxCode']             = $this->getNonTaxable();
			$postRow['rowValue']['rowNet']['value']     = '0.00';
			$postRow['rowValue']['rowTax']['value']     = '0.00';
			
			$reference		= $order->getIncrementId();
 			$responses = $this->_api->postOrderRow($bpOrderId, $postRow);  //  call add order row api
			$salesOrderRowId = '';
            if (array_key_exists('response', $responses)) {
                $salesOrderRowId = $responses['response'];
            }

            if (isset($responses['response']) and $salesOrderRowId > 0) {
                $log_data = 'Order Comment row added succeesfully';	//  Insert Log
                $this->recordLog($cat = "Order", $log_data, $reference);
            } else {
                $log_data = 'Order Comment posting failed';	//  Insert Log
                $this->recordLog($cat = "Order", $log_data, $reference);
            }
        }
    }
      
        /*Reduce Coupon Code Amount from Order*/
    public function CouponCodeDiscount($order, $bpOrderId)
    {
                    
                $reference = $order->getIncrementId();
                $discount_amount         = $order->getDiscountAmount();
                $discount_description     = $order->getDiscountDescription();
                     
        if ($order->getDiscountAmount() && $order->getDiscountAmount() < 0) {
            if ($order->getDiscountTaxCompensationAmount() > 0) {
                         $discount_amount    =  $discount_amount + $order->getDiscountTaxCompensationAmount();
                          $taxCode            =  $this->getConfig('bpconfiguration/bp_orderconfig/taxcode');
                         $rowTax                =  $order->getDiscountTaxCompensationAmount() * -1 ;
                         $rowTotal            = $discount_amount;
            } else {
                   $taxCode                 = $this->getNonTaxable();
                   $rowTax                 = '0.00';
                   $rowTotal                 = $discount_amount;
            }
                // discount_tax_compensation_amount
                         
                     $discount_row = [];
                     $discount_row['productName']                     = 'Discount : ' . $discount_description;
                     $discount_row['quantity']['magnitude']             = 1;
                     $discount_row['rowValue']['taxCode']             = $taxCode;
                     $discount_row['rowValue']['rowNet']['value']     = $rowTotal;
                     $discount_row['rowValue']['rowTax']['value']     = $rowTax;
                        
                     $nominalCode = $this->getDiscountNominalCode();
                        
            if ($nominalCode != "" && $nominalCode != null) {
                $discount_row['nominalCode'] = trim($nominalCode);
            }

                     $log_data = 'Discount : ' . json_encode($discount_row);
                     $this->recordLog($cat = "Order", $log_data, $title = $reference);

                     $responses = $this->_api->postOrderRow($bpOrderId, $discount_row);  //  call add order row api
                     $salesOrderRowId = $responses['response'];
                        
            if (isset($responses['response']) and $salesOrderRowId > 0) {
                $log_data = 'Discount row added successfully : '.$discount_description.' Discount Price : '.$rowTotal;      //  Insert Log
                $this->recordLog($cat = "Order", $log_data, $title = $reference);
            } else {
                $log_data = 'There are no discount row';      //  Insert Log
                $this->recordLog($cat = "Order", $log_data, $title = $reference);
            }
        }
    }


        /*Reduce Store Credit / Customer Balance Amount from Order*/
    public function CustomerBalanceAmount($order, $bpOrderId)
    {
            
        $reference             = $order->getIncrementId();
        $postAmount            = $order->getCustomerBalanceAmount();
        $postDescription     = 'Store Credit';
            
        if ($postAmount && $postAmount > 0) {
            $taxCode                 = $this->getNonTaxable();
            $rowTax                 = '0.00';
            $rowTotal                = - $postAmount;
                 
            $postRow = [];
            $postRow['productName']                     = $postDescription;
            $postRow['quantity']['magnitude']             = 1;
            $postRow['rowValue']['taxCode']             = $taxCode;
            $postRow['rowValue']['rowNet']['value']     = $rowTotal;
            $postRow['rowValue']['rowTax']['value']     = $rowTax;
                
            $nominalCode = $this->getStoreCreditNominalCode();
                
            if ($nominalCode != "" && $nominalCode != null) {
                $postRow['nominalCode'] = trim($nominalCode);
            }

            $log_data = json_encode($postRow);
            $this->recordLog("Order", $log_data, $reference);

            $responses = $this->_api->postOrderRow($bpOrderId, $postRow);  //  call add order row api
            $salesOrderRowId = $responses['response'];
                
            if (isset($responses['response']) and $salesOrderRowId > 0) {
                $log_data = 'Store Credit or Customer Balance row added successfully. Price : '.$rowTotal; //-Insert Log
                $this->recordLog("Order", $log_data, $reference);
            } else {
                $log_data = 'There are no discount row';      //  Insert Log
                $this->recordLog("Order", $log_data, $reference);
            }
        }
    }


        /* add rounding difference row in order */

    public function addRoundingDifferencAmountRow($order, $bpOrderId)
    {
        $postAmount     = 0 ;
        $orderTotal     = $order->getGrandTotal() ;
        $invoicedTotal     = $order->getTotalInvoiced();
        if ($invoicedTotal != $orderTotal) {
            $postAmount = $orderTotal - $invoicedTotal ;
        }
             
        $postAmount = number_format($postAmount, '2', '.', '');
            
        $isRoundingEnable = $this->getRoundingEnable();
        $roundingThresholdLimit = $this->getRoundingThreshold();
        $nominalCode = $this->getRoundingSkuNominalCode();
            
                                     
                    $this->recordLog("Order", $postAmount, $order->getIncrementId());

            
        $postAmount1         = $postAmount ;
        $absRounbdingAmount = abs($postAmount1);
        if ($isRoundingEnable and $absRounbdingAmount < $roundingThresholdLimit) {
            if ($postAmount != 0) {
                  $reference             = $order->getIncrementId();
                  $postDescription     = 'Rounding Adjustment';
                  $taxCode                 = $this->getNonTaxable();
                  $rowTax                 = '0.00';
                  $rowTotal                = $postAmount;
                     
                  $postRow = [];
                  $postRow['productName']                     = $postDescription;
                  $postRow['quantity']['magnitude']             = 1;
                  $postRow['rowValue']['taxCode']             = $taxCode;
                  $postRow['rowValue']['rowNet']['value']     = $rowTotal;
                  $postRow['rowValue']['rowTax']['value']     = $rowTax;
                if ($nominalCode != "" && $nominalCode != null) {
                    $postRow['nominalCode'] = trim($nominalCode);
                }

                  $log_data = json_encode($postRow);
                  $this->recordLog("Order", $log_data, $reference);

                  $responses = $this->_api->postOrderRow($bpOrderId, $postRow);  //  call add order row api
                  $salesOrderRowId = $responses['response'];
                    
                if (isset($responses['response']) and $salesOrderRowId > 0) {
                    $log_data = 'Rounding Adjustment row added successfully. Price : '.$rowTotal; //-Insert Log
                    $this->recordLog("Order", $log_data, $reference);
                } else {
                    $log_data = 'There are no discount row';      //  Insert Log
                    $this->recordLog("Order", $log_data, $reference);
                }
            }
        }
    }





    /*Reduce Coupon Code Amount from Order*/
    public function OrderShippingCharge($order, $bpOrderId)
    {
        if ($order->getShippingTaxAmount()) {
                $reference = $order->getIncrementId();
                $nominalCode     = $this->getShippingNominalCode(); # Get from configuratiom
                $taxCode         = $this->getNonTaxable();  # N for Non Taxable
                $rowTax         = '0.00';
            if ($order->getShippingTaxAmount() && $order->getShippingTaxAmount() > 0) {
                $taxCode     = $this->getTaxable(); #  T For Taxable
                $rowTax     = number_format($order->getShippingTaxAmount(), '2', '.', '');
            }

                $rowTotal = number_format($order->getShippingAmount(), '2', '.', '');
                $prow = [];
                $prow['productName'] = 'Shipping Method - '.$order->getShippingDescription();
                $prow['quantity']['magnitude'] = 1;
                $prow['rowValue']['taxCode'] = $taxCode;
                $prow['rowValue']['rowNet']['value'] = $rowTotal;
                $prow['rowValue']['rowTax']['value'] = $rowTax;
            if ($nominalCode != "" && $nominalCode != null) {
                $prow['nominalCode'] = trim($nominalCode);
            }

                $responses = $this->_api->postOrderRow($bpOrderId, $prow);  //  call add order row api
                $salesOrderRowId = '';
            if (array_key_exists('response', $responses)) {
                $salesOrderRowId = $responses['response'];
            }

            if (isset($responses['response']) and $salesOrderRowId > 0) {
                $log_data = 'Shipping Method row added succeesfully title : '.$order->getShippingDescription().' Price : '.$rowTotal;      //  Insert Log
                $this->recordLog($cat = "Order", $log_data, $title = $reference);
            } else {
                $log_data = 'Shipping charges are not applied';      //  Insert Log
                $this->recordLog($cat = "Order", $log_data, $title = $reference);
            }
        }
    }


        /*code inventory reservation for ordered product*/
    public function setInventoryToOrderRow($order, $incrementid, $bporderId, $salesOrderRowId, $reservationProductArray)
    {
        $responses = '';
        if (count($reservationProductArray) > 0) {
            $param = '{	products:[ '.implode(",", $reservationProductArray).' ] } ';
                
            $warehouse = $this->getBpWarehouse($order);
            /*Reserve Inventory for Order Row and Assign in products*/
                
            $response = $this->_api->postInventoryReservation($bporderId, $warehouse, $param); //call inventroty reservation api
            /*Log for Inventory*/
            $log_data = "ReserverInverntory ". json_encode($response);
            $this->recordLog($cat = "Order", $log_data, $title = $incrementid);

            /*Check Inventory for products*/
            $responses = $this->_api->checkInventoryReservation($bporderId, $param);
            $log_data = "Check ReserverInverntory ". json_encode($responses);
            $this->recordLog($cat = "Order", $log_data, $title = $incrementid);
        }
        return $responses;
    }



    public function getPosOrderPayment($order)
    {
        $data = '';
         $paymentsFactory = $this->_objectManager->create('\Magestore\Webpos\Model\ResourceModel\Sales\Order\Payment\CollectionFactory');
        $paymentsCollection = $paymentsFactory->create()->addFieldToFilter('order_id', $order->getId())
        ->addFieldToFilter('type', \Magestore\Webpos\Api\Data\Payment\PaymentInterface::ORDER_TYPE);
        if ($paymentsCollection->getSize()) {
              $data  = $paymentsCollection->getFirstItem();
        }
            return $data;
    }

        /*Payment Received for Order*/
    public function postCustomerPayment($Bapp, $order, $bpOrderId, $brightpearlUserId)
    {
            
        /*Get payment Methods in Magento 2*/
        $payment       = $order->getPayment();
        $paymentcode  =  $payment->getMethod();
            
            
        /* ------------- if pos order then then get method from pos object ------*/
        $posid = $order->getPosStaffId();
        if ($posid) {
            $pos_payment = '';
             $pos_payment = $this->getPosOrderPayment($order);
            if ($pos_payment) {
                 $paymentcode  = $pos_payment->getMethod();
            }
        }
        /* ------------- if pos order then then get method from pos object ------*/
             

        $reference = $order->getIncrementId();

        $bankAccountNominalCode = '';
            
        if ($paymentcode) {
            $bankAccountNominalCode = $this->getMapPaymentMethod($paymentcode);
        }
            
        /// --------- get exchange rate ---------------
 
        $orderTotal = number_format($order->getGrandTotal(), '2', '.', '');
        $description = 'Payment Received against the Magento Order #'.$order->getIncrementId();

        /// -------- create sales receipt array   --------------------
          $paymentsArray = [];
          $paymentsArray['paymentMethodCode'] = $bankAccountNominalCode;
        $paymentsArray['paymentType'] = 'RECEIPT';
        $paymentsArray['orderId'] = $bpOrderId;
        $paymentsArray['currencyIsoCode'] = $order->getOrderCurrencyCode();
        //$paymentsArray['exchangeRate'] = $exchangeRate;
        $paymentsArray['amountPaid'] = $orderTotal;
        $paymentsArray['paymentDate'] = date("Y-m-d");
        $paymentsArray['journalRef'] = $description;

        /*Post Payment Data in logs files*/
        $log_data = "Order Payment Data ". json_encode($paymentsArray);
        $this->recordLog($cat = "Order", $log_data, $title = $reference);

         /// -------- post sales receipt array   --------------------
        $response = $Bapp->postCustomerPayment($paymentsArray);

        return $response ;
    }
        
        
        /* ------------------- Payment Received for Order from store credit or customer balance ---------------------- */
    public function postStoreCreditPayment($Bapp, $order, $bpOrderId, $brightpearlUserId)
    {
            
        $reference                     = $order->getIncrementId();
        $postAmount                    = number_format($order->getCustomerBalanceAmount(), '2', '.', '');
        $bankAccountNominalCode     = '';
             
        if ($postAmount && $postAmount > 0) {
            $paymentcode            =  'store_credit';
            $description             = 'Store Credit Payment Received against the Magento Order #'.$reference;
            $bankAccountNominalCode = $this->getMapPaymentMethod($paymentcode);
             /// -------- create sales receipt array   --------------------
            $paymentsArray = [];
            $paymentsArray['paymentMethodCode'] = $bankAccountNominalCode;
            $paymentsArray['paymentType'] = 'RECEIPT';
            $paymentsArray['orderId'] = $bpOrderId;
            $paymentsArray['currencyIsoCode'] = $order->getOrderCurrencyCode();
             $paymentsArray['amountPaid'] = $postAmount;
            $paymentsArray['paymentDate'] = date("Y-m-d");
            $paymentsArray['journalRef'] = $description;

            /*Post Payment Data in logs files*/
            $log_data = "Order Store Credit Payment Data ". json_encode($paymentsArray);
            $this->recordLog("Order", $log_data, $reference);
             /// -------- post sales receipt array   --------------------
            $response = $Bapp->postCustomerPayment($paymentsArray);
             return $response ;
        }
    }
        
        
        /* ------------------- Payment Received for Order from Gift Crds Amount or customer balance ---------------------- */
    public function postGiftCardsPayment($Bapp, $order, $bpOrderId, $brightpearlUserId)
    {
            
        $reference                     = $order->getIncrementId();
        $postAmount                    = number_format($order->getGiftCardsAmount(), '2', '.', '');
        $bankAccountNominalCode     = '';
             
        if ($postAmount && $postAmount > 0) {
            $paymentcode            =  'gift_voucher';
            $description             = 'Gift Crds Payment Received against the Magento Order #'.$reference;
            $bankAccountNominalCode = $this->getMapPaymentMethod($paymentcode);
             /// -------- create sales receipt array   --------------------
            $paymentsArray = [];
            $paymentsArray['paymentMethodCode'] = $bankAccountNominalCode;
            $paymentsArray['paymentType'] = 'RECEIPT';
            $paymentsArray['orderId'] = $bpOrderId;
            $paymentsArray['currencyIsoCode'] = $order->getOrderCurrencyCode();
             $paymentsArray['amountPaid'] = $postAmount;
            $paymentsArray['paymentDate'] = date("Y-m-d");
            $paymentsArray['journalRef'] = $description;

            /*Post Payment Data in logs files*/
            $log_data = "Order Gift Crds Payment Data ". json_encode($paymentsArray);
            $this->recordLog("Order", $log_data, $reference);
             /// -------- post sales receipt array   --------------------
            $response = $Bapp->postCustomerPayment($paymentsArray);
             return $response ;
        }
    }
        
        
        
        
        
    
    
    public function getBpCustomerInfoArray($obj1, $telephone = "")
    {
            /*Call pricelist from configurations*/

            $priceListId = '';
        if ($this->getPricelist()) {
            $priceListId = $this->getPricelist();
        } else {
            $priceListId = '';
        }

        if ($telephone == "") {
            if (array_key_exists('telephone', $obj1)) {
                $telephone = $obj1['telephone'];
            }
             // $telephone = $obj1['telephone'];
        }

            $custInfo = [];
            $custInfo['salutation'] = '';
            $custInfo['firstName'] = $obj1['firstname'];
            $custInfo['lastName'] = $obj1['lastname'];
            $custInfo['postAddressIds'] = '';
            $custInfo['communication']['emails']['PRI'] = ['email' => $obj1['email']];
            $custInfo['communication']['telephones']['PRI'] = $telephone;
            $custInfo['communication']['websites']['PRI'] = '';
            $custInfo['marketingDetails']['isReceiveEmailNewsletter'] = true;
            $custInfo['financialDetails']['priceListId'] = $priceListId;
            return $custInfo;
    }

    /* Create a BP Address Arrays */
    public function getAddressArray($objAddress, $tmpObj = "")
    {
            $country = $this->_countryFactory->create()->loadByCode($objAddress['country_id']);
            $country = $country->getData();
            $isocode = $country['iso3_code'];
            $bpAddress     = [];
            
        if ($tmpObj) {
             $street =  $tmpObj->getStreet();
            if (is_array($street)) {
                $street1 = '';
                $street2 = '';
                if (array_key_exists("0", $street)) {
                    $street1 = trim($street[0]);
                }
                if (array_key_exists("1", $street)) {
                    $street2 = $street[1];
                }
                if ($street1 =="") {
                    $street1 = $objAddress['street'];
                }
                    
                $bpAddress['addressLine1'] = $street1;
                $bpAddress['addressLine2'] = $street2;
            } else {
                $bpAddress['addressLine1'] = $objAddress['street'];
            }
        } else {
             # Street
            $bpAddress['addressLine1'] = $objAddress['street'];
            # Suburb
            //$bpAddress['addressLine2'] = $objAddress->getStreet(2);
        }
            # City
            $bpAddress['addressLine3'] = $objAddress['city'];
            # County/State
            $bpAddress['addressLine4'] = $objAddress['region'];
            # Postcode/Zipcode
            $bpAddress['postalCode'] = $objAddress['postcode'];
            # LookupCountry
            $bpAddress['countryIsoCode'] = $isocode;
            return $bpAddress;
    }
    
    
    /*Get payment Methods Bank Account Nominal Code */
    public function getPaymentBankAccountNominalCode($paymentcode, $currencyCode)
    {
        $enable  =  $this->getConfig('bpconfiguration/bp_orderconfig/enable');
        $paymentcode = trim($paymentcode);
        $mpnominalcode = '';
        if ($enable) {
            $paymentcollections = $this->_bppaymentmapFactory->create()->getCollection();
            $paymentcollections = $paymentcollections->addFieldToFilter('code', $paymentcode);
            if ($paymentcollections->getSize()) {
                $data  = $paymentcollections->getFirstItem();
                $bpcode = $data->getBpcode();
                if ($bpcode) {
                     $bpPayment         = $this->_objectManager->create('Bsitc\Brightpearl\Model\BppaymentFactory');
                    $collections     = $bpPayment->create()->getCollection();
                    $collections     = $collections->addFieldToFilter('code', $bpcode);
                    if ($collections->getSize()) {
                        $result          = $collections->getFirstItem();
                        $bankAccounts     = json_decode($result->getBankAccounts(), true);
                        foreach ($bankAccounts as $bankAccount) {
                            if ($bankAccount['currencyIsoCode'] == $currencyCode) {
                                $mpnominalcode =     $bankAccount['bankAccountNominalCode'];
                                break;
                            }
                        }
                    }
                }
            }
        }
        return $mpnominalcode;
    }
	
	
	public function postOrderCustomAttributes($order, $bporderId)
	{
		$bpData = array();
		$othercustomattr  =  $this->getConfig('bpconfiguration/bp_orderconfig/order_custom_attribute');
		if($othercustomattr)
		{
			$orderCustomAttributeData = $this->getOrderCustomAttributeData($order->getId());
 			$step1 = explode("#",$othercustomattr);
			foreach($step1 as $step2)
			{
				$step3 = explode(":",$step2);
				$key  = trim($step3[0]);
				if (array_key_exists($key , $orderCustomAttributeData))
				{
					$path = trim($step3[1]);
 					$value = trim($orderCustomAttributeData[$key]);
					if($value){
						$bpData[] =  ['op'=> 'add', 'path' => '/'.$path, 'value' => $value];
						//$bpData[] =  ['op'=> 'replace', 'path' => '/'.$path, 'value' => $value];
					}
				}
			}
		}
		
		if(count($bpData)>0)
		{
			$log_data = "Post Order Custom Fields Data ". json_encode($bpData, true);
			$this->recordLog("Order", $log_data, $order->getIncrementId());

			$result = $this->_api->postOrderCustomAttribute($bporderId, $bpData);  /* Post Order Custom Fields */
			
			$log_data = "Post Order Custom Fields Response ". json_encode($result, true);
			$this->recordLog("Order", $log_data, $order->getIncrementId());

		}
		return true;
       
    }
	
	public function getOrderCustomAttributeData($orderId)
	{
		$orderCustomAttributeData = array();
		$orderCustomAttributes = array();
		
 		$attributeCollection  = $this->_objectManager->create('\Aitoc\CheckoutFieldsManager\Model\ResourceModel\Attribute\CollectionFactory'); 
		$collection = $attributeCollection->create();
		foreach ($collection as $item) {
 			$orderCustomAttributes[$item->getAttributeId()] = $item->getAttributeCode();
		}

		if (count($orderCustomAttributes) > 0 )
		{
			$atObj = $this->_objectManager->create('\Aitoc\CheckoutFieldsManager\Model\ResourceModel\OrderCustomerData\Collection'); 
			$result = $atObj->getAitocCheckoutfieldsByOrderId($orderId); 
			foreach ($result  as $item)
			{
				$increment_id 	= $item['increment_id'];
				$code 			= $orderCustomAttributes[$increment_id];
				$value 			= $item['value'];
				if ( $item['type'] == 'select' ) 
				{
					foreach ( $item['options'] as $option){
						if ($option['value'] == $value ){
							$value = $option['label'];
							break;
						}
					}
				}
				if( $item['type'] == 'date' and $value != "")
				{
					$value = date("Y-m-d", strtotime($value)); 
				}
				if( $item['type'] == 'boolean')
				{
					if($value == 1) { $value = true; } else{  $value = false; }
				}
				$orderCustomAttributeData[$code] = $value ; 
			}		
		}
		return $orderCustomAttributeData;
	}
	
		
 	
	
}
