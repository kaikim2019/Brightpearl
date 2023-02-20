<?php
namespace Bsitc\Brightpearl\Controller\Index;

class Bpapi extends \Magento\Framework\App\Action\Action
{
    
    protected $_api;

    protected $_restapi;
    
    protected $_productapi;

    protected $_categoryapi;
    
    protected $_pricelistconfig;

    protected $_customattribute;

    protected $_datahelper;

    protected $_brandapi;

    protected $_attributeapi;

    protected $_pricelistapi;

    protected $_producturi;

    protected $_bpwarehouse;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Bsitc\Brightpearl\Model\Api $api,
        \Bsitc\Brightpearl\Model\RestApi $restapi,
        \Bsitc\Brightpearl\Model\BpproductsFactory $productapi,
        \Bsitc\Brightpearl\Model\CategoryFactory $categoryapi,
        \Bsitc\Brightpearl\Model\PricelistconfigFactory $pricelistconfig,
        \Bsitc\Brightpearl\Model\CustomattributeFactory $customattribute,
        \Bsitc\Brightpearl\Model\AttributeFactory $attributeapi,
        \Bsitc\Brightpearl\Model\BrandFactory $brandapi,
        \Bsitc\Brightpearl\Model\PricelistFactory $pricelistapi,
        \Bsitc\Brightpearl\Model\ProducturiFactory $producturi,
        \Bsitc\Brightpearl\Model\BpwarehouseFactory $bpwarehouse,
        \Bsitc\Brightpearl\Helper\Data $datahelper
    ) {
        $this->_api              = $api;
        $this->_restapi          = $restapi;
        $this->_productapi       = $productapi;
        $this->_categoryapi      = $categoryapi;
        $this->_pricelistconfig  = $pricelistconfig;
        $this->_customattribute  =  $customattribute;
        $this->_attributeapi     = $attributeapi;
        $this->_brandapi         = $brandapi;
        $this->_producturi       = $producturi;
        $this->_bpwarehouse       = $bpwarehouse;
        $this->_pricelistapi     = $pricelistapi;
        $this->_datahelper          = $datahelper;
        parent::__construct($context);
    }
    
    public function execute()
    {
		/*
		$camf  = $this->_restapi->_objectManager->create('\Bsitc\Brightpearl\Model\ConfigattributemapFactory');
		$mgtAttributes  = $camf->getMgtAttributes();
		$bpAttributes  = $camf->getBpAttributes();
		echo '<pre>mgtAttributes'; print_r($mgtAttributes); echo '</pre>'; 
		echo '<pre>bpAttributes '; print_r($bpAttributes); echo '</pre>'; 


		$bp_id = '157018';
		$data = $this->_api->getProductById($bp_id);
		$responses = $data['response'];
		
		$variations = $responses[0]['variations'];
		
		$variations  = json_encode($variations);

		$variations  = json_decode($variations,true);
		
 		
		foreach($variations as $variation)
		{
			$optionName = trim($variation['optionName']);
			if (array_key_exists($optionName,$mgtAttributes))
			{
				$optionValueId = $variation['optionValueId']; 
				$mgtCode = $mgtAttributes[$optionName]; 
				$mgtOptionId = $this->getBpAttributeoptionId($mgtCode, $optionValueId);
				$this->_productaction->updateAttributes($arrayid, [$mgtCode => $mgtOptionId], 0);
			}

					
			echo '<pre>'; print_r($variation); echo '</pre>';  
		}
		
		
		echo '<pre>'; print_r($variations); echo '</pre>'; die;
		*/
		
		
        $this->_attributeapi->setAttributeOptions();
        $this->_attributeapi->setSeasonOptions();
        $this->_attributeapi->setBpSeasonOptions();
        $this->_brandapi->syncBrandApi();
        /*fetch all category from brightpearls*/
        $this->_categoryapi->setCategoryApi();
        $code = 'bp_collection';
        $this->_customattribute->syncCustomattributeApi($code);
        /*Are used to fetch all products uri data from brightpearls*/
        $this->_producturi->setProducturiApi();
        $this->_bpwarehouse->setBpwarehouse();
        $this->_pricelistconfig->getAllPriceListConfig();
        /*Sync Products at first time from brightpearl to Magento*/
        /*$this->_productapi->syncBpProductsByrangeApi();
        $this->_pricelistapi->getPricelistByProductApi();*/
    }
}
