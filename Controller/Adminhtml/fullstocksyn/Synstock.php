<?php

namespace Bsitc\Brightpearl\Controller\Adminhtml\fullstocksyn;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;

class Synstock extends \Magento\Backend\App\Action
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
        $model = $this->_objectManager->create('Bsitc\Brightpearl\Model\FullstocksynFactory');
        $model->synchronize();
         $this->messageManager->addSuccess(__('Stock synchronization has been successfully done.'));
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/');
    }
}
