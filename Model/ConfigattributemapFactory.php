<?php

namespace Bsitc\Brightpearl\Model;

class ConfigattributemapFactory
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
        return $this->_objectManager->create('Bsitc\Brightpearl\Model\Configattributemap', $arguments, false);
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
	
	public function getBpAttributes($case ='')
	{
		$attributes = array();
		$collection = $this->create()->getCollection();
		if ($collection->getSize()) {
			foreach ($collection as $item) {
				if($case){
					$attributes[$item->getMgtCode()] = trim($item->getBpCode());
				}else{
					$attributes[strtolower($item->getMgtCode())] = trim($item->getBpCode());
				}
 			}			
		}
		return $attributes;
	}
	
	public function getMgtAttributes($case ='')
	{
		$attributes = array();
		$collection = $this->create()->getCollection();
		if ($collection->getSize()) {
			foreach ($collection as $item) {
				if($case){
					$attributes[strtolower($item->getBpCode())] = trim($item->getMgtCode());
				}else{
					$attributes[$item->getBpCode()] = trim($item->getMgtCode());
				}
 			}			
		}
		return $attributes;
	}
	
	
	
    
	
	
}