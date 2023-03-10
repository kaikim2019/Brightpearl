<?php

namespace Bsitc\Brightpearl\Controller\Adminhtml\reconciliation;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;

class Executereport extends \Magento\Backend\App\Action
{
    /**
     * @param Action\Context $context
     */
    public function __construct(Action\Context $context)
    {
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {	
        $model = $this->_objectManager->create('Bsitc\Brightpearl\Model\ReconciliationFactory');
        $model->executeReport();
        $this->messageManager->addSuccess(__('Stock reconciliation report refresh successfully.'));
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/');
    }
}
