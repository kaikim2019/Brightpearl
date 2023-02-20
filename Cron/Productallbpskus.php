<?php

namespace Bsitc\Brightpearl\Cron;
 
class Productallbpskus
{
    protected $logger;
    protected $api;
    protected $log;
    protected $bpitemsFactory;

  
    public function __construct(
        \Psr\Log\LoggerInterface $loggerInterface,
        \Bsitc\Brightpearl\Model\Api $brightpearlApi,
        \Bsitc\Brightpearl\Model\LogsFactory $log,
        \Bsitc\Brightpearl\Model\BpitemsFactory $bpitemsFactory
    ) {
        $this->logger = $loggerInterface;
        $this->api = $brightpearlApi;
        $this->log = $log;
        $this->bpitemsFactory = $bpitemsFactory;
    }
 
    public function execute()
    {
        $cron_config = $this->api->bpcron;
        if (array_key_exists("enable", $cron_config) && $cron_config['enable'] == '1') {
               /* Product Webhooks Cron */
               $this->bpitemsFactory->synBpProducts();
				$log = $this->log->create();
					$log->setCategory('Cron')
                     ->setTitle('BP API Queue for all product SKU fetch Cron')
                    ->setError("RUNNING cron in configuration")
                    ->setStoreId(1)
                    ->save();
        } else {
            $log = $this->log->create();
            $log->setCategory('Cron')
                     ->setTitle('BP API Queue for all product SKU fetch Cron')
                    ->setError("Disable cron in configuration")
                    ->setStoreId(1)
                    ->save();
        }
    }
}
