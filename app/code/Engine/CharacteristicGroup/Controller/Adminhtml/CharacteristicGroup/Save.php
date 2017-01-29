<?php
namespace Engine\CharacteristicGroup\Controller\Adminhtml\CharacteristicGroup;

use Engine\CharacteristicGroup\Api\Data\CharacteristicGroupInterface;
use Engine\CharacteristicGroup\Api\Data\CharacteristicGroupInterfaceFactory;
use Engine\CharacteristicGroup\Api\CharacteristicGroupRepositoryInterface;
use Engine\Validation\Exception\ValidatorException;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class Save extends Action
{
    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Engine_CharacteristicGroup::characteristic_group_characteristic_group';

    /**
     * Registry characteristic_group_id key
     */
    const REGISTRY_CHARACTERISTIC_GROUP_ID_KEY = 'characteristic_group_id';

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
     * @var Registry
     */
    private $registry;

    /**
     * @param Context $context
     * @param CharacteristicGroupInterfaceFactory $characteristicGroupFactory
     * @param CharacteristicGroupRepositoryInterface $characteristicGroupRepository
     * @param HydratorInterface $hydrator
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        CharacteristicGroupInterfaceFactory $characteristicGroupFactory,
        CharacteristicGroupRepositoryInterface $characteristicGroupRepository,
        HydratorInterface $hydrator,
        Registry $registry
    ) {
        parent::__construct($context);
        $this->characteristicGroupFactory = $characteristicGroupFactory;
        $this->characteristicGroupRepository = $characteristicGroupRepository;
        $this->hydrator = $hydrator;
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $requestData = $this->getRequest()->getParam('general');
        if ($this->getRequest()->isPost() && $requestData) {
            try {
                $useDefaults = $this->getRequest()->getParam('use_default', []);
                if ($useDefaults) {
                    foreach ($useDefaults as $field => $useDefaultState) {
                        if (1 === (int)$useDefaultState) {
                            $requestData[$field] = null;
                        }
                    }
                }
                $characteristicGroupId = !empty($requestData[CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID])
                    ? $requestData[CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID] : null;

                if ($characteristicGroupId) {
                    $characteristicGroup = $this->characteristicGroupRepository->get($characteristicGroupId);
                } else {
                    /** @var CharacteristicGroupInterface $characteristicGroup */
                    $characteristicGroup = $this->characteristicGroupFactory->create();
                }
                $characteristicGroup = $this->hydrator->hydrate($characteristicGroup, $requestData);
                $characteristicGroupId = $this->characteristicGroupRepository->save($characteristicGroup);
                // Keep data for plugins on Save controller. Now we can not call separate services from one form.
                $this->registry->register(self::REGISTRY_CHARACTERISTIC_GROUP_ID_KEY, $characteristicGroupId);

                $this->messageManager->addSuccessMessage(__('The Characteristic Group has been saved.'));
                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath('*/*/edit', [
                        CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID => $characteristicGroupId,
                        '_current' => true,
                    ]);
                } elseif ($this->getRequest()->getParam('redirect_to_new')) {
                    $resultRedirect->setPath('*/*/new', [
                        '_current' => true,
                    ]);
                } else {
                    $resultRedirect->setPath('*/*/');
                }
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('The Characteristic Group does not exist.'));
                $resultRedirect->setPath('*/*/');
            } catch (ValidatorException $e) {
                foreach ($e->getErrors() as $error) {
                    $this->messageManager->addErrorMessage($error);
                }
                if (!empty($characteristicGroupId)) {
                    $resultRedirect->setPath('*/*/edit', [
                        CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID => $characteristicGroupId,
                        '_current' => true,
                    ]);
                } else {
                    $resultRedirect->setPath('*/*/');
                }
            } catch (CouldNotSaveException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                if (!empty($characteristicGroupId)) {
                    $resultRedirect->setPath('*/*/edit', [
                        CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID => $characteristicGroupId,
                        '_current' => true,
                    ]);
                } else {
                    $resultRedirect->setPath('*/*/');
                }
            }
        } else {
            $this->messageManager->addErrorMessage(__('Wrong request.'));
            $resultRedirect->setPath('*/*');
        }
        return $resultRedirect;
    }
}
