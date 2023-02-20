<?php
namespace Bsitc\Brightpearl\Observer\Backend\Catalog;
 
use Magento\Framework\Event\ObserverInterface;
 
class ProductSaveAfter implements ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {       
	   $product = $observer->getProduct();		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		//$stockRegistry = $objectManager->get('Magento\CatalogInventory\Api\StockRegistryInterface')->getStockItem($product->getId());
		$remote = $objectManager->get('Magento\Framework\HTTP\PhpEnvironment\RemoteAddress');
		//$stockData = $stockRegistry->getData();        
        $sku = $product->getSku();
		$id = $product->getId();
        $name = $product->getName();		
		$status = $product->getStatus();
    }
}