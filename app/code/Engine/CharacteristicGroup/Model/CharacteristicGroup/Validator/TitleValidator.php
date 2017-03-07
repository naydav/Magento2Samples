<?php
namespace Engine\CharacteristicGroup\Model\CharacteristicGroup\Validator;

use Engine\CharacteristicGroup\Api\Data\CharacteristicGroupInterface;
use Engine\CharacteristicGroup\Model\CharacteristicGroup\CharacteristicGroupValidatorInterface;
use Engine\Validation\Exception\ValidatorException;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class TitleValidator implements CharacteristicGroupValidatorInterface
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
    public function validate(CharacteristicGroupInterface $characteristicGroup)
    {
        $storeId = (int)$this->storeManager->getStore()->getId();
        $value = (string)$characteristicGroup->getTitle();

        if ((Store::DEFAULT_STORE_ID === $storeId || !$this->entityManager->has($characteristicGroup))
            && '' === $value
        ) {
            $errors[] = __('"%1" can not be empty.', CharacteristicGroupInterface::TITLE);
            throw new ValidatorException($errors);
        }
    }
}
