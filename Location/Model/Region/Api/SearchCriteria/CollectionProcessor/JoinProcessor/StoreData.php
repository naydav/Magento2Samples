<?php
namespace Engine\Location\Model\Region\Api\SearchCriteria\CollectionProcessor\JoinProcessor;

use Engine\Backend\Api\StoreContextInterface;
use Engine\Location\Model\Region\ResourceModel\RegionCollection;
use Magento\Framework\Api\SearchCriteria\CollectionProcessor\JoinProcessor\CustomJoinInterface;
use Magento\Framework\Data\Collection\AbstractDb;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class StoreData implements CustomJoinInterface
{
    /**
     * @var StoreContextInterface
     */
    private $storeContext;

    /**
     * @param StoreContextInterface $storeContext
     */
    public function __construct(
        StoreContextInterface $storeContext
    ) {
        $this->storeContext = $storeContext;
    }

    /**
     * @param AbstractDb|RegionCollection $collection
     * @return bool
     */
    public function apply(AbstractDb $collection)
    {
        $storeId = $this->storeContext->getCurrentStore()->getId();
        $collection->addStoreData($storeId);
        return true;
    }
}
