<?php

namespace Bsitc\Brightpearl\Controller\Adminhtml\mgtcreditmemo;

use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;

class ExportCsv extends \Magento\Backend\App\Action
{
    protected $_fileFactory;

    public function execute()
    {
        $this->_view->loadLayout(false);

        $fileName = 'mgtcreditmemo.csv';

        $exportBlock = $this->_view->getLayout()->createBlock('Bsitc\Brightpearl\Block\Adminhtml\Mgtcreditmemo\Grid');

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $this->_fileFactory = $objectManager->create('Magento\Framework\App\Response\Http\FileFactory');

        return $this->_fileFactory->create(
            $fileName,
            $exportBlock->getCsvFile(),
            DirectoryList::VAR_DIR
        );
    }
}
