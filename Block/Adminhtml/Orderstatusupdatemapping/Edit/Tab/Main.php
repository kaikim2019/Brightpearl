<?php

namespace Bsitc\Brightpearl\Block\Adminhtml\Orderstatusupdatemapping\Edit\Tab;

/**
 * Orderstatusupdatemapping edit form main tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Bsitc\Brightpearl\Model\Status
     */
    protected $_status;
	
	protected $_allsalesstatus;
	protected $_mgtorderstatus;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Bsitc\Brightpearl\Model\Status $status,
        \Bsitc\Brightpearl\Model\Config\Source\Allsalesstatus $allsalesstatus,
        \Bsitc\Brightpearl\Model\Config\Source\Allmgtorderstatus $mgtorderstatus,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_status = $status;
        $this->_allsalesstatus = $allsalesstatus;
        $this->_mgtorderstatus = $mgtorderstatus;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        /* @var $model \Bsitc\Brightpearl\Model\BlogPosts */
        $model = $this->_coreRegistry->registry('orderstatusupdatemapping');

        $isElementDisabled = false;

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Item Information')]);

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }
		
        $fieldset->addField('bpo_status_id','select',[
                'name' => 'bpo_status_id',
                'label' => __('BP Order Status'),
                'title' => __('BP Order Status'),
                'type' => 'options',
                'required' => true,
                'options' => $this->_allsalesstatus->toArray(),
                'disabled' => $isElementDisabled
            ]
        );
 		
        $fieldset->addField('mgto_status_code','select',[
                'name' => 'mgto_status_code',
                'label' => __('Mgt Order Status'),
                'title' => __('Mgt Order Status'),
                'type' => 'options',
                'required' => true,
                'options' => $this->_mgtorderstatus->toArray(),
                'disabled' => $isElementDisabled
            ]
        );
					
		/*		
        $fieldset->addField('bpo_status_name','text',[
                'name' => 'bpo_status_name',
                'label' => __('bpo_status_name'),
                'title' => __('bpo_status_name'),
                'disabled' => $isElementDisabled
            ]
        );
 
        $fieldset->addField('mgto_status_name','text',[
                'name' => 'mgto_status_name',
                'label' => __('mgto_status_name'),
                'title' => __('mgto_status_name'),
                'disabled' => $isElementDisabled
            ]
        );
					
        $fieldset->addField('store_id','text',[
                'name' => 'store_id',
                'label' => __('store_id'),
                'title' => __('store_id'),
                'disabled' => $isElementDisabled
            ]
        );
					
        $fieldset->addField('created_at','text',[
                'name' => 'created_at',
                'label' => __('created_at'),
                'title' => __('created_at'),
                'disabled' => $isElementDisabled
            ]
        );
					
        $fieldset->addField('updated_at','text',[
                'name' => 'updated_at',
                'label' => __('updated_at'),
                'title' => __('updated_at'),
                'disabled' => $isElementDisabled
            ]
        );
		*/
		

        if (!$model->getId()) {
            $model->setData('is_active', $isElementDisabled ? '0' : '1');
        }

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Item Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Item Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    public function getTargetOptionArray(){
    	return array('_self' => "Self",'_blank' => "New Page",);
    }
}
