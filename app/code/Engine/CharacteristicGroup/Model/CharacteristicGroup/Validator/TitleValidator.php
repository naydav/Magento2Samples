<?php
namespace Engine\CharacteristicGroup\Model\CharacteristicGroup\Validator;

use Engine\CharacteristicGroup\Api\Data\CharacteristicGroupInterface;
use Engine\CharacteristicGroup\Model\CharacteristicGroup\CharacteristicGroupValidatorInterface;
use Engine\Validation\Exception\ValidatorException;
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
    public function validate(CharacteristicGroupInterface $characteristicGroup)
    {
        $storeId = (int)$this->storeManager->getStore()->getId();

        $errors = [];
        $value = (string)$characteristicGroup->getTitle();
        if ((Store::DEFAULT_STORE_ID === $storeId || !$characteristicGroup->getCharacteristicGroupId())
            && '' === $value
        ) {
            $errors[] = __('"%1" can not be empty.', CharacteristicGroupInterface::TITLE);
        }

        if (count($errors)) {
            throw new ValidatorException($errors);
        }
    }
}
