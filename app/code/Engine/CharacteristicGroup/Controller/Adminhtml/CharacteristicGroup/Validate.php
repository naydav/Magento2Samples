<?php
namespace Engine\CharacteristicGroup\Controller\Adminhtml\CharacteristicGroup;

use Engine\CharacteristicGroup\Api\Data\CharacteristicGroupInterface;
use Engine\CharacteristicGroup\Api\Data\CharacteristicGroupInterfaceFactory;
use Engine\CharacteristicGroup\Api\CharacteristicGroupRepositoryInterface;
use Engine\CharacteristicGroup\Model\CharacteristicGroup\CharacteristicGroupValidatorInterface;
use Engine\MagentoFix\Exception\ValidatorException;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class Validate extends Action
{
    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Engine_CharacteristicGroup::characteristic_group_characteristic_group';

    /**
     * @var CharacteristicGroupInterfaceFactory
     */
    private $characteristicGroupFactory;

    /**
     * @var CharacteristicGroupRepositoryInterface
     */
    private $characteristicGroupRepository;

    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @var CharacteristicGroupValidatorInterface
     */
    private $characteristicGroupValidator;

    /**
     * @param Context $context
     * @param CharacteristicGroupInterfaceFactory $characteristicGroupFactory
     * @param CharacteristicGroupRepositoryInterface $characteristicGroupRepository
     * @param HydratorInterface $hydrator
     * @param CharacteristicGroupValidatorInterface $characteristicGroupValidator
     */
    public function __construct(
        Context $context,
        CharacteristicGroupInterfaceFactory $characteristicGroupFactory,
        CharacteristicGroupRepositoryInterface $characteristicGroupRepository,
        HydratorInterface $hydrator,
        CharacteristicGroupValidatorInterface $characteristicGroupValidator
    ) {
        parent::__construct($context);
        $this->characteristicGroupFactory = $characteristicGroupFactory;
        $this->characteristicGroupRepository = $characteristicGroupRepository;
        $this->hydrator = $hydrator;
        $this->characteristicGroupValidator = $characteristicGroupValidator;
    }

    /**
     * @return Json
     */
    public function execute()
    {
        $errorMessages = [];
        $request = $this->getRequest();
        $requestData = $this->getRequest()->getParam('general');

        if ($request->isXmlHttpRequest() && $request->isPost() && $requestData) {
            $characteristicGroupId = !empty($requestData[CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID])
                ? $requestData[CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID] : null;

            try {
                if ($characteristicGroupId) {
                    $characteristicGroup = $this->characteristicGroupRepository->get($characteristicGroupId);
                } else {
                    /** @var CharacteristicGroupInterface $characteristicGroup */
                    $characteristicGroup = $this->characteristicGroupFactory->create();
                }
                $characteristicGroup = $this->hydrator->hydrate($characteristicGroup, $requestData);
                $this->characteristicGroupValidator->validate($characteristicGroup);
            } catch (NoSuchEntityException $e) {
                $errorMessages[] = __('The CharacteristicGroup does not exist.');
            } catch (ValidatorException $e) {
                $errorMessages = $e->getErrors();
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
