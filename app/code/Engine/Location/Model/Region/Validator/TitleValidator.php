<?php
namespace Engine\Location\Model\Region\Validator;

use Engine\Location\Api\Data\RegionInterface;
use Engine\Location\Model\Region\RegionValidatorInterface;
use Engine\Validation\Exception\ValidatorException;
use Magento\Framework\EntityManager\EntityManager;
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
    public function validate(RegionInterface $region)
    {
        $storeId = (int)$this->storeManager->getStore()->getId();
        $value = (string)$region->getTitle();

        if ((Store::DEFAULT_STORE_ID === $storeId || !$this->entityManager->has($region)) && '' === $value) {
            $errors[] = __('"%1" can not be empty.', RegionInterface::TITLE);
            throw new ValidatorException($errors);
        }
    }
}
