<?php
namespace Bsitc\Brightpearl\Model;
class BpitemsFactory
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    public $_logManager;
    public $_api;
    public $_scopeConfig;
    protected $_resource;
    protected $_connection;
	protected $_webhookupdate;	
	

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Bsitc\Brightpearl\Model\Api $api,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\ResourceConnection $resource,
        \Bsitc\Brightpearl\Model\LogsFactory $logManager,
		\Bsitc\Brightpearl\Model\WebhookupdateFactory $webhookupdate		
		
    ) {
        $this->_objectManager = $objectManager;
        $this->_api             = $api;
        $this->_logManager      = $logManager;
        $this->_scopeConfig     = $scopeConfig;
        $this->_resource        = $resource;
		$this->_webhookupdate   = $webhookupdate;
        $this->_connection        = $this->_resource->getConnection();		
    }

    /**
     * Create new country model
     *
     * @param array $arguments
     * @return \Magento\Directory\Model\Country
     */
    public function create(array $arguments = [])
    {
        return $this->_objectManager->create('Bsitc\Brightpearl\Model\Bpitems', $arguments, false);
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
		//$this->_logManager->recordLog($column.$value, $type, "callig caame");
        $data = '';
        $collection = $this->create()->getCollection()->addFieldToFilter($column, $value);
        if ($collection->getSize()) {
            $data  = $collection->getFirstItem();
        }
		
		//$this->_logManager->recordLog(json_encode($data, true), $type, "Product Event Search");
		
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
    
    
    public function synBpProducts()
    {
		$this->synBpProductsCustom();
		return true;
		
		
        $bsbpi = $this->_resource->getTableName('bsitc_brightpearl_bpitems');
        //$this->_connection->truncateTable($bsbpi);

         
         // $items = $this->_api->getAllBrightpearlProducts('product-availability');
        $collection = $this->_api->getAllBrightpearlProducts();
        if (count($collection) > 0) {
            foreach ($collection as $items) {
                foreach ($items as $item) {
                    $bpStatus = $item['status'];
                    if ($bpStatus == 'LIVE') {
                        $row =[];
                        $row['bp_id']         = $item['id'];
                        $row['bp_sku']         = $item['identity']['sku'];
                        $row['bp_ptype']     = ($item['composition']['bundle'] > 0 ? 1 : 0);
                        $this->addRecord($row);
                    }
                }
            }
        }
          return true;
    }
	
    public function synBpProductsCustom()
    {

		/* $this->getLatestBrightpearlUpdatedProducts();

		die('<br>finish ..........'); */
	
		$isAliasSkuEnable = $this->isAliasSkuEnable();
		$aliasSkuCode = $this->getAliasSkuCode();
		
        $bsbpi = $this->_resource->getTableName('bsitc_brightpearl_bpitems');
		$bs_webhook_table = $this->_resource->getTableName('bsitc_brightpearl_webhooks_update');
        $this->_connection->truncateTable($bsbpi);

		$url		= $this->_api->brightURL.'/product-service/product';
		$method		= 'OPTIONS';
		$data		= [];
        $json		= true;
        $callTitle	= 'Get All Brightpearl Products';
        $response =  $this->_api->getCommonResponse($url, $method, $data, $json, $callTitle);
        $data = json_decode($response);		
		
        $collection = [];		
		if ($data->response->getUris ) 
		{
            foreach ($data->response->getUris as $key => $value) 
			{
				// $this->_logManager->recordLog(json_encode($value, true), $key, "BP Items key");
				
				if($isAliasSkuEnable){
					$apiUrl = $this->_api->brightURL.'/product-service'.$value."?includeOptional=customFields,nullCustomFields";	
				}else{
					$apiUrl = $this->_api->brightURL.'/product-service'.$value;
				}
                $tmpMethod		= 'GET';
                $tmpData		= [];
                $tmpJson		= true;
                $tmpCallTitle	= 'Get All Brightpearl Products';
                $tmpResponse 	= $this->_api->getCommonResponse($apiUrl, $tmpMethod, $tmpData, $tmpJson, $tmpCallTitle);
                $tmpData 		= json_decode($tmpResponse, true);				
                $items 	= $tmpData['response'];				
				$batchData = [];				
				$webhook_table_batchData = [];
				
				if(count($items)){
					foreach ($items as $item) {
						$bpStatus = $item['status'];
						if ($bpStatus == 'LIVE') {
							$row =[];						
							if($isAliasSkuEnable)
							{							
								if(!empty($aliasSkuCode) && isset($item['customFields'][$aliasSkuCode]))
								{								
									$row['bp_id']		= $item['id'];
									$row['bp_sku']		= $item['customFields'][$aliasSkuCode];
									//$row['bp_sku']		= 	$item['identity']['sku'];
									$row['bp_ptype']	= ($item['composition']['bundle'] > 0 ? 1 : 0);
									$row['updated_at']	= date("Y-m-d H:i:s");	
									$row['bp_org_sku'] = $item['identity']['sku'];							
									if( trim($item['customFields'][$aliasSkuCode]) !=""){							
										$batchData[] = $row;							
									}

								}							
							}else{								
								$row['bp_id']		= $item['id'];
								$row['bp_sku']		= $item['identity']['sku'];
								$row['bp_ptype']	= ($item['composition']['bundle'] > 0 ? 1 : 0);
								$row['updated_at']	= date("Y-m-d H:i:s");								
								if( trim($item['identity']['sku']) !=""){
								//$this->addRecord($row);
								$batchData[] = $row;							
								}							
							}							
						}
					}	
				}else{
					$this->_logManager->recordLog('Bpitems Fetch data null' ,$apiUrl,'');	
				}				
				if(count($batchData))
				{
				$this->_connection->insertMultiple($bsbpi, $batchData);
 				usleep(1000000); /* 1 sec = 1000000 microseconds  */
				}
            }
        }		

		/* udpate webhook table with new product id fetch */			
			$sql = "SELECT A.bp_id 	FROM $bsbpi AS A LEFT JOIN $bs_webhook_table AS B ON A.bp_id = B.bp_id where B.bp_id IS NULL";
			$result = $this->_connection->fetchAll($sql);
			if(count($result))
			{	
				$webhook_table_batchData = array();
				$i = 0;
				foreach($result as $row)
				{
					//echo $row['bp_id'];
					$webhookdata = array();
					$webhookdata['bp_id']           = $row['bp_id'];
					$webhookdata['account_code']    = $this->getAccountId(); //$item['accountCode'];
					$webhookdata['resource_type']   = 'product';
					$webhookdata['lifecycle_event'] = 'modified';//$item['lifecycleEvent'];
					$webhookdata['full_event']      = 'product.modified'; //$item['fullEvent'];
					$webhookdata['sync']            = 0;
					$webhookdata['status']          = 'pending';
					$webhookdata['created_at']      = date('Y-m-d H:i:s');							
					$webhook_table_batchData[] = $webhookdata;
					
				}				
				$this->_connection->insertMultiple($bs_webhook_table, $webhook_table_batchData);
				usleep(1000000); 
			}			
		/* end of webhook table update */

		return true;
    }
	
	public function productEvent($data, $type)
    {        
        if ($type == 'created') {
            $pid = $data['id'];
            if ($pid) {
                $result = $this->_api->getProductById($pid);
                if (array_key_exists("response", $result)) {
                    $response = $result['response'][0];
                    
                    $bpStatus = $response['status'];
                    if ($bpStatus == 'LIVE') {
                        /*  $row =[];
                        $row['bp_id']         = $response['id'];
                        $row['bp_sku']         = $response['identity']['sku'];
                        $row['bp_ptype']     = $response['composition']['bundle'];
                        $this->addRecord($row); */
						
						$isAliasSkuEnable = $this->isAliasSkuEnable();
						$aliasSkuCode = $this->getAliasSkuCode();						
						if($isAliasSkuEnable)
						{							
							if(!empty($aliasSkuCode) && isset($response['customFields'][$aliasSkuCode]))
							{	
								$row =[];
								$row['bp_id']		= $response['id'];
								$row['bp_sku']		= $response['customFields'][$aliasSkuCode];								
								$row['bp_ptype']	= ($response['composition']['bundle'] > 0 ? 1 : 0);									
								$row['bp_org_sku'] = $response['identity']['sku'];							
								if( trim($response['customFields'][$aliasSkuCode]) !="")
								{							
									$this->addRecord($row);							
								}								
							}							
						}else{
							
							$row =[];
							$row['bp_id']         = $response['id'];
							$row['bp_sku']         = $response['identity']['sku'];
							$row['bp_ptype']     = $response['composition']['bundle'];
							$this->addRecord($row);							
						}						
                    }
                }
            }
        }		
		
		if ($type == 'modified') {
            $pid = $data['id'];
            if ($pid) {
                $result = $this->_api->getProductById($pid);
                if (array_key_exists("response", $result)) {
                    $response = $result['response'][0];
                    
                    $bpStatus = $response['status'];
                    if ($bpStatus == 'LIVE') {
						$search = $this->findRecord('bp_id', $pid);
						$id = false;
						if($search != ""){
							$id = $search->getId();		
						}						
						if($id ){						
							$isAliasSkuEnable = $this->isAliasSkuEnable();
							$aliasSkuCode = $this->getAliasSkuCode();						
							if($isAliasSkuEnable)
							{							
								if(!empty($aliasSkuCode) && isset($response['customFields'][$aliasSkuCode]))
								{	
								
									
									$row =[];
									$row['bp_id']		= $response['id'];
									$row['bp_sku']		= $response['customFields'][$aliasSkuCode];								
									$row['bp_ptype']	= ($response['composition']['bundle'] > 0 ? 1 : 0);									
									$row['bp_org_sku'] = $response['identity']['sku'];							
									if( trim($response['customFields'][$aliasSkuCode]) !="")
									{							
										$this->updateRecord($id, $row);							
									}								
								}							
							}else{
								
								$row =[];
								$row['bp_id']         = $response['id'];
								$row['bp_sku']         = $response['identity']['sku'];
								$row['bp_ptype']     = $response['composition']['bundle'];
								$this->updateRecord($id, $row);							
							}	
					 }else{
						 
						 $this->productEvent($data, 'created');
					 }

						
                    }
                }
            }
        }
        
        if ($type == 'destroyed') {
            $pid = $data['id'];
            if ($pid) {
                $search = $this->findRecord('bp_id', $pid);
                $this->_logManager->recordLog(json_encode($search->getData(), true), $type, "Product Event Search");
                if ($search) {
                    $rowId = $search->getId();
                    $this->removeRecord($rowId);
                }
            }
        }
        
        return true;
    }
	
	public function  isAliasSkuEnable()
	{
		$path = "bpconfiguration/bpproduct/use_alias_sku";		
		return $this->_scopeConfig->getValue(
											$path,
											\Magento\Store\Model\ScopeInterface::SCOPE_STORE
											);
	}
	
	public function getAliasSkuCode()
	{		
		$path = "bpconfiguration/bpproduct/alias_sku_attribute";
		return $this->_scopeConfig->getValue(
											$path,
											\Magento\Store\Model\ScopeInterface::SCOPE_STORE
											);
	}
	
	public function getAccountId()
	{
		$path = "bpconfiguration/api/bp_account_id";		
		return $this->_scopeConfig->getValue(
											$path,
											\Magento\Store\Model\ScopeInterface::SCOPE_STORE
											);
	}
	
}
