<?php
namespace Bsitc\Brightpearl\Block\Adminhtml\Webhookinventory;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Bsitc\Brightpearl\Model\webhookinventoryFactory
     */
    protected $_webhookinventoryFactory;

    /**
     * @var \Bsitc\Brightpearl\Model\Status
     */
    protected $_status;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Bsitc\Brightpearl\Model\webhookinventoryFactory $webhookinventoryFactory
     * @param \Bsitc\Brightpearl\Model\Status $status
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Bsitc\Brightpearl\Model\WebhookinventoryFactory $WebhookinventoryFactory,
        \Bsitc\Brightpearl\Model\Status $status,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        $this->_webhookinventoryFactory = $WebhookinventoryFactory;
        $this->_status = $status;
        $this->moduleManager = $moduleManager;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('postGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(false);
        $this->setVarNameFilter('post_filter');
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_webhookinventoryFactory->create()->getCollection();
        $this->setCollection($collection);

        parent::_prepareCollection();

        return $this;
    }

    /**
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'id',
            [
                'header' => __('ID'),
                'type' => 'number',
                'index' => 'id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );


        
                $this->addColumn(
                    'account_code',
                    [
                        'header' => __('Account Code'),
                        'index' => 'account_code',
                    ]
                );
                
                $this->addColumn(
                    'bp_id',
                    [
                        'header' => __('BrightPearl ID'),
                        'index' => 'bp_id',
                    ]
                );
                
                
                $this->addColumn(
                    'mgt_id',
                    [
                        'header' => __('Magento Product ID'),
                        'index' => 'mgt_id',
                    ]
                );
                
                $this->addColumn(
                    'sku',
                    [
                        'header' => __('Sku'),
                        'index' => 'sku',
                    ]
                );

                /*$this->addColumn(
                    'lifecycle_event',
                    [
                        'header' => __('Lifecycle Event'),
                        'index' => 'lifecycle_event',
                    ]
                );

                $this->addColumn(
                    'full_event',
                    [
                        'header' => __('Full Event'),
                        'index' => 'full_event',
                    ]
                );*/
/*
                $this->addColumn(
                    'old_inventory',
                    [
                        'header' => __('Previous Inventory'),
                        'index' => 'old_inventory',
                    ]
                );



                $this->addColumn(
                    'updated_inventory',
                    [
                        'header' => __('Updated Inventory'),
                        'index' => 'updated_inventory',
                    ]
                );
                */
                
                $this->addColumn(
                    'status',
                    [
                        'header' => __('Status'),
                        'index' => 'status',
                    ]
                );
                
    
                $this->addColumn(
                    'updated_at',
                    [
                        'header' => __('Updated At'),
                        'index' => 'updated_at',
                        'type'      => 'datetime',
                    ]
                );
 
        
           $this->addExportType($this->getUrl('brightpearl/*/exportCsv', ['_current' => true]), __('CSV'));
           $this->addExportType($this->getUrl('brightpearl/*/exportExcel', ['_current' => true]), __('Excel XML'));

        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }

        return parent::_prepareColumns();
    }

    
    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {

        $this->setMassactionIdField('id');
        //$this->getMassactionBlock()->setTemplate('Bsitc_Brightpearl::webhookinventory/grid/massaction_extended.phtml');
        $this->getMassactionBlock()->setFormFieldName('webhookinventory');
         $this->getMassactionBlock()->addItem(
             'delete',
             [
                'label' => __('Delete'),
                'url' => $this->getUrl('brightpearl/*/massDelete'),
                'confirm' => __('Are you sure?')
             ]
         );
        
        
       //  $statuses = $this->_status->getOptionArray();
        
        $statuses = ['pending'=> __('Pending'), 'processing' => __('Processing'), 'error' => __('Error'), 'complete' => __('Complete')];
         
        $this->getMassactionBlock()->addItem(
            'status',
            [
                'label' => __('Change status'),
                'url' => $this->getUrl('brightpearl/*/massStatus', ['_current' => true]),
                'additional' => [
                    'visibility' => [
                        'name' => 'status',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => __('Status'),
                        'values' => $statuses
                    ]
                ]
            ]
        );
        
        
        
        
        
        
        
        
        
        
         return $this;
    }
        

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('brightpearl/*/index', ['_current' => true]);
    }

    /**
     * @param \Bsitc\Brightpearl\Model\webhookinventory|\Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('brightpearl/*/edit', ['id' => $row->getId()]);
    }
}
