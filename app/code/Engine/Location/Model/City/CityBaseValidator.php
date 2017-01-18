<?php
namespace Engine\Location\Model\City;

use Engine\Location\Api\Data\CityInterface;
use Engine\Framework\Exception\ValidatorException;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class CityBaseValidator implements CityBaseValidatorInterface
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
    public function validate(CityInterface $city)
    {
        $storeId = (int)$this->storeManager->getStore()->getId();

        $errors = [];
        $value = (string)$city->getTitle();
        if ((Store::DEFAULT_STORE_ID === $storeId || !$city->getCityId()) && '' === $value) {
            $errors[] = __('"%1" can not be empty.', CityInterface::TITLE);
        }

        if (count($errors)) {
            throw new ValidatorException($errors);
        }
    }
}
