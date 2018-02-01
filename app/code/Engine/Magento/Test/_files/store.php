<?php
declare(strict_types=1);

use Magento\Framework\Api\DataObjectHelper;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Api\Data\StoreInterfaceFactory;
use Magento\TestFramework\Helper\Bootstrap;

/** @var StoreInterfaceFactory $storeFactory */
$storeFactory = Bootstrap::getObjectManager()->get(StoreInterfaceFactory::class);
/** @var DataObjectHelper $dataHelper */
$dataObjectHelper = Bootstrap::getObjectManager()->get(DataObjectHelper::class);
/** @var StoreInterface $store */
$store = $storeFactory->create();

$dataObjectHelper->populateWithArray(
    $store,
    [
        'code' => 'test_store',
        'website_id' => 1,
        'group_id' => 1,
        'name' => 'Test Store',
        'sort_order' => 0,
        'is_active' => 1,
    ],
    StoreInterface::class
);
$store->save();
