<?php

namespace Bsitc\Brightpearl\Model\ResourceModel\Bpproductimage;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Bsitc\Brightpearl\Model\Bpproductimage', 'Bsitc\Brightpearl\Model\ResourceModel\Bpproductimage');
        $this->_map['fields']['page_id'] = 'main_table.page_id';
    }
}
