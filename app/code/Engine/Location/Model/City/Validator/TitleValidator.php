<?php
namespace Engine\Location\Model\City\Validator;

use Engine\Location\Api\Data\CityInterface;
use Engine\Location\Model\City\CityValidatorInterface;
use Engine\Validation\Exception\ValidatorException;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class TitleValidator implements CityValidatorInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param StoreManagerInterface $storeManager
     * @param EntityManager $entityManager
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        EntityManager $entityManager
    ) {
        $this->storeManager = $storeManager;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(CityInterface $city)
    {
        $storeId = (int)$this->storeManager->getStore()->getId();
        $value = (string)$city->getTitle();

        if ((Store::DEFAULT_STORE_ID === $storeId || !$this->entityManager->has($city)) && '' === $value) {
            $errors[] = __('"%1" can not be empty.', CityInterface::TITLE);
            throw new ValidatorException($errors);
        }
    }
}
