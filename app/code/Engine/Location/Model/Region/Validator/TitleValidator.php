<?php
namespace Engine\Location\Model\Region\Validator;

use Engine\Location\Api\Data\RegionInterface;
use Engine\Location\Model\Region\RegionValidatorInterface;
use Engine\Validation\Exception\ValidatorException;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class TitleValidator implements RegionValidatorInterface
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
     * {@inheritdoc}
     */
    public function validate(RegionInterface $region)
    {
        $storeId = (int)$this->storeManager->getStore()->getId();

        $errors = [];
        $value = (string)$region->getTitle();
        if ((Store::DEFAULT_STORE_ID === $storeId || !$region->getRegionId()) && '' === $value) {
            $errors[] = __('"%1" can not be empty.', RegionInterface::TITLE);
        }

        if (count($errors)) {
            throw new ValidatorException($errors);
        }
    }
}
