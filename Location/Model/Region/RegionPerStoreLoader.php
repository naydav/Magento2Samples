<?php
namespace Engine\Location\Model\Region;

use Engine\Backend\Api\StoreContextInterface;
use Engine\Location\Api\Data\RegionInterface;
use Engine\Location\Api\RegionRepositoryInterface;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class RegionPerStoreLoader
{
    /**
     * @var StoreContextInterface
     */
    private $storeContext;

    /**
     * @var RegionRepositoryInterface
     */
    private $regionRepository;

    /**
     * @param StoreContextInterface $storeContext
     * @param RegionRepositoryInterface $regionRepository
     */
    public function __construct(
        StoreContextInterface $storeContext,
        RegionRepositoryInterface $regionRepository
    ) {
        $this->storeContext = $storeContext;
        $this->regionRepository = $regionRepository;
    }

    /**
     * @param int $id
     * @param int $storeId
     * @return RegionInterface
     */
    public function load($id, $storeId)
    {
        $currentStore = $this->storeContext->getCurrentStore();

        $this->storeContext->setCurrentStoreById($storeId);
        $entity = $this->regionRepository->get($id);
        $this->storeContext->setCurrentStoreById($currentStore->getId());
        return $entity;
    }
}
