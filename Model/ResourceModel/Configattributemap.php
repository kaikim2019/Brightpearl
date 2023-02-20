<?php
namespace Bsitc\Brightpearl\Model\ResourceModel;

class Configattributemap extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('bsitc_cp_attribute_map', 'id');
    }
}