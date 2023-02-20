<?php

namespace Bsitc\Brightpearl\Block\Adminhtml\Mgtcreditmemo\Edit\Tab;

/**
 * Mgtcreditmemo edit form main tab
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
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_status = $status;
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
        $model = $this->_coreRegistry->registry('mgtcreditmemo');

        $isElementDisabled = false;

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Item Information')]);

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }

        
        $fieldset->addField(
            'mgt_order_id',
            'text',
            [
                'name' => 'mgt_order_id',
                'label' => __('mgt_order_id'),
                'title' => __('mgt_order_id'),
                
                'disabled' => $isElementDisabled
            ]
        );
                    
        $fieldset->addField(
            'mgt_creditmemo_id',
            'text',
            [
                'name' => 'mgt_creditmemo_id',
                'label' => __('mgt_creditmemo_id'),
                'title' => __('mgt_creditmemo_id'),
                
                'disabled' => $isElementDisabled
            ]
        );
                    
        $fieldset->addField(
            'so_order_id',
            'text',
            [
                'name' => 'so_order_id',
                'label' => __('so_order_id'),
                'title' => __('so_order_id'),
                
                'disabled' => $isElementDisabled
            ]
        );
                    
        $fieldset->addField(
            'sc_order_id',
            'text',
            [
                'name' => 'sc_order_id',
                'label' => __('sc_order_id'),
                'title' => __('sc_order_id'),
                
                'disabled' => $isElementDisabled
            ]
        );
                    
        $fieldset->addField(
            'sc_payment_status',
            'text',
            [
                'name' => 'sc_payment_status',
                'label' => __('sc_payment_status'),
                'title' => __('sc_payment_status'),
                
                'disabled' => $isElementDisabled
            ]
        );
                    
        $fieldset->addField(
            'status',
            'text',
            [
                'name' => 'status',
                'label' => __('status'),
                'title' => __('status'),
                
                'disabled' => $isElementDisabled
            ]
        );
                    

        $dateFormat = $this->_localeDate->getDateFormat(
            \IntlDateFormatter::MEDIUM
        );
        $timeFormat = $this->_localeDate->getTimeFormat(
            \IntlDateFormatter::MEDIUM
        );

        $fieldset->addField(
            'created_at',
            'date',
            [
                'name' => 'created_at',
                'label' => __('Created At'),
                'title' => __('Created At'),
                    'date_format' => $dateFormat,
                    //'time_format' => $timeFormat,
                
                'disabled' => $isElementDisabled
            ]
        );
                        
                        
                        

        $dateFormat = $this->_localeDate->getDateFormat(
            \IntlDateFormatter::MEDIUM
        );
        $timeFormat = $this->_localeDate->getTimeFormat(
            \IntlDateFormatter::MEDIUM
        );

        $fieldset->addField(
            'updated_at',
            'date',
            [
                'name' => 'updated_at',
                'label' => __('Updated At'),
                'title' => __('Updated At'),
                    'date_format' => $dateFormat,
                    //'time_format' => $timeFormat,
                
                'disabled' => $isElementDisabled
            ]
        );
                        
                        
                        

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
    
    public function getTargetOptionArray()
    {
        return [
                    '_self' => "Self",
                    '_blank' => "New Page",
                    ];
    }
}
