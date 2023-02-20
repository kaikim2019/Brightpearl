<?php
namespace Bsitc\Brightpearl\Block\Adminhtml\Bpitems;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Bsitc\Brightpearl\Model\bpitemsFactory
     */
    protected $_bpitemsFactory;

    /**
     * @var \Bsitc\Brightpearl\Model\Status
     */
    protected $_status;
	
	protected $_bpconfig;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Bsitc\Brightpearl\Model\bpitemsFactory $bpitemsFactory
     * @param \Bsitc\Brightpearl\Model\Status $status
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Bsitc\Brightpearl\Model\BpitemsFactory $BpitemsFactory,
        \Bsitc\Brightpearl\Model\Status $status,
        \Magento\Framework\Module\Manager $moduleManager,
		\Bsitc\Brightpearl\Helper\Config $bpConfig,
        array $data = []
    ) {
        $this->_bpitemsFactory = $BpitemsFactory;
        $this->_status = $status;
        $this->moduleManager = $moduleManager;
		$this->_bpconfig = $bpConfig;
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
        $collection = $this->_bpitemsFactory->create()->getCollection();
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
            'bp_id',
            [
                'header' => __('BP Id'),
                'index' => 'bp_id',
            ]
        );
        
        /* $this->addColumn(
            'bp_sku',
            [
                'header' => __('BP Sku'),
                'index' => 'bp_sku',
            ]
        ); */
		
		if($this->_bpconfig->isAliasSkuEnable())
		{
			$this->addColumn(
				'bp_sku',
				[
					'header' => __('BP Alias SKU Mapped to use'),
					'index' => 'bp_sku'
				]
			);	
			
			$this->addColumn(
				'bp_org_sku',
				[
					'header' => __('BP Standard SKU'),
					'index' => 'bp_org_sku'
				]
			);
		}else{
			$this->addColumn(
				'bp_sku',
				[
					'header' => __('BP Sku'),
					'index' => 'bp_sku'
				]
			);			
		}
        
       /*  $this->addColumn(
            'bp_ptype',
            [
                'header' => __('Bundle'),
                'index' => 'bp_ptype',
            ]
        ); */
		
        
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
        $this->getMassactionBlock()->setFormFieldName('bpitems');

        $this->getMassactionBlock()->addItem(
            'delete',
            [
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
     * @param \Bsitc\Brightpearl\Model\bpitems|\Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        
        // return $this->getUrl('brightpearl/*/edit',['id' => $row->getId()] );
    }
}
