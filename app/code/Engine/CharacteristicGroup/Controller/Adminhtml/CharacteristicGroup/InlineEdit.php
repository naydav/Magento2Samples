<?php
namespace Engine\CharacteristicGroup\Controller\Adminhtml\CharacteristicGroup;

use Engine\CharacteristicGroup\Api\CharacteristicGroupRepositoryInterface;
use Engine\CharacteristicGroup\Api\Data\CharacteristicGroupInterface;
use Engine\Validation\Exception\ValidatorException;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class InlineEdit extends Action
{
    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Engine_CharacteristicGroup::characteristic_group_characteristic_group';

    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @var CharacteristicGroupRepositoryInterface
     */
    private $characteristicGroupRepository;

    /**
     * @param Context $context
     * @param HydratorInterface $hydrator
     * @param CharacteristicGroupRepositoryInterface $characteristicGroupRepository
     */
    public function __construct(
        Context $context,
        HydratorInterface $hydrator,
        CharacteristicGroupRepositoryInterface $characteristicGroupRepository
    ) {
        parent::__construct($context);
        $this->hydrator = $hydrator;
        $this->characteristicGroupRepository = $characteristicGroupRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $errorMessages = [];
        $request = $this->getRequest();
        $requestData = $request->getParam('items', []);

        if ($request->isXmlHttpRequest() && $request->isPost() && $requestData) {
            foreach ($requestData as $itemData) {
                try {
                    $characteristicGroup = $this->characteristicGroupRepository->get(
                        $itemData[CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID]
                    );
                    $characteristicGroup = $this->hydrator->hydrate($characteristicGroup, $itemData);
                    $this->characteristicGroupRepository->save($characteristicGroup);
                } catch (NoSuchEntityException $e) {
                    $errorMessages[] = __(
                        '[ID: %1] The Characteristic Group does not exist.',
                        $itemData[CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID]
                    );
                } catch (ValidatorException $e) {
                    $errorMessages = $e->getErrors();
                } catch (CouldNotSaveException $e) {
                    $errorMessages[] =
                        __('[ID: %1] ', $itemData[CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID])
                        . $e->getMessage();
                }
            }
        } else {
            $errorMessages[] = __('Please correct the data sent.');
        }

        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData([
            'messages' => $errorMessages,
            'error' => count($errorMessages),
        ]);
        return $resultJson;
    }
}
