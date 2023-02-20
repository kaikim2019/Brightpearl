<?php
namespace Bsitc\Brightpearl\Block\Adminhtml\Orderstatusupdatemapping;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Bsitc\Brightpearl\Model\orderstatusupdatemappingFactory
     */
    protected $_orderstatusupdatemappingFactory;

    /**
     * @var \Bsitc\Brightpearl\Model\Status
     */
    protected $_status;
	
	protected $_allsalesstatus;
	protected $_mgtorderstatus;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Bsitc\Brightpearl\Model\orderstatusupdatemappingFactory $orderstatusupdatemappingFactory
     * @param \Bsitc\Brightpearl\Model\Status $status
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Bsitc\Brightpearl\Model\OrderstatusupdatemappingFactory $OrderstatusupdatemappingFactory,
        \Bsitc\Brightpearl\Model\Status $status,
        \Magento\Framework\Module\Manager $moduleManager,
        \Bsitc\Brightpearl\Model\Config\Source\Allsalesstatus $allsalesstatus,
        \Bsitc\Brightpearl\Model\Config\Source\Allmgtorderstatus $mgtorderstatus,		
        array $data = []
    ) {
        $this->_orderstatusupdatemappingFactory = $OrderstatusupdatemappingFactory;
        $this->_status = $status;
        $this->_allsalesstatus = $allsalesstatus;
        $this->_mgtorderstatus = $mgtorderstatus;
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
        $collection = $this->_orderstatusupdatemappingFactory->create()->getCollection();
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
        $this->addColumn('id',[
                'header' => __('ID'),
                'type' => 'number',
                'index' => 'id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

  
			
        $this->addColumn('bpo_status_id', [
                'header' => __('BP Order Status => '),
                'index'  => 'bpo_status_id',
                'type'   =>'options',
                 'options' => $this->_allsalesstatus->toArray(),
            ]);
			
			
        $this->addColumn('mgto_status_code', [
                'header' => __('MGT Order Status'),
                'index'  => 'mgto_status_code',
                'type'   =>'options',
                 'options' => $this->_mgtorderstatus->toArray(),
            ]);

 		
		$this->addColumn('created_at',[
				'header' => __('Created At'),
				'index' => 'created_at',
			]
		);
		
		$this->addColumn('updated_at',[
				'header' => __('Updated At'),
				'index' => 'updated_at',
			]
		);
	
	   $this->addExportType($this->getUrl('brightpearl/*/exportCsv', ['_current' => true]),__('CSV'));
	   $this->addExportType($this->getUrl('brightpearl/*/exportExcel', ['_current' => true]),__('Excel XML'));

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
        $this->getMassactionBlock()->setFormFieldName('orderstatusupdatemapping');
        $this->getMassactionBlock()->addItem('delete',[
                'label' => __('Delete'),
                'url' => $this->getUrl('brightpearl/*/massDelete'),
                'confirm' => __('Are you sure?')
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
     * @param \Bsitc\Brightpearl\Model\orderstatusupdatemapping|\Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('brightpearl/*/edit', ['id' => $row->getId()]);
    }
	

}