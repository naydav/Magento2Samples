<?php
namespace Engine\Location\Model\Region;

use Engine\Location\Api\Data\RegionInterface;
use Engine\Framework\Exception\ValidatorException;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class RegionBaseValidator
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    /**
     * @param RegionInterface $region
     * @return void
     * @throws ValidatorException
     */
    public function validate(RegionInterface $region)
    {
        $storeId = (int)$this->storeManager->getStore()->getId();

        $errors = [];
        $value = (string)$region->getTitle();
        if (Store::DEFAULT_STORE_ID === $storeId && '' === $value) {
            $errors[] = __('"%1" can not be empty.', RegionInterface::TITLE);
        }

        if (count($errors)) {
            throw new ValidatorException(__('Entity isn\'t valid.'), $errors);
        }
    }
}
