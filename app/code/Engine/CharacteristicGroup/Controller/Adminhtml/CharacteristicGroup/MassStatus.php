<?php
namespace Engine\CharacteristicGroup\Controller\Adminhtml\CharacteristicGroup;

use Engine\CharacteristicGroup\Api\Data\CharacteristicGroupInterface;
use Engine\CharacteristicGroup\Api\CharacteristicGroupRepositoryInterface;
use Engine\CharacteristicGroup\Model\CharacteristicGroup\ResourceModel\CharacteristicGroupCollectionFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Ui\Component\MassAction\Filter;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class MassStatus extends Action
{
    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Engine_CharacteristicGroup::characteristic_group_characteristic_group';

    /**
     * @var CharacteristicGroupRepositoryInterface
     */
    private $characteristicGroupRepository;

    /**
     * @var Filter
     */
    private $massActionFilter;

    /**
     * @var CharacteristicGroupCollectionFactory
     */
    private $characteristicGroupCollectionFactory;

    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @param Context $context
     * @param CharacteristicGroupRepositoryInterface $characteristicGroupRepository
     * @param Filter $massActionFilter
     * @param CharacteristicGroupCollectionFactory $characteristicGroupCollectionFactory
     * @param HydratorInterface $hydrator
     */
    public function __construct(
        Context $context,
        CharacteristicGroupRepositoryInterface $characteristicGroupRepository,
        Filter $massActionFilter,
        CharacteristicGroupCollectionFactory $characteristicGroupCollectionFactory,
        HydratorInterface $hydrator
    ) {
        parent::__construct($context);
        $this->characteristicGroupRepository = $characteristicGroupRepository;
        $this->massActionFilter = $massActionFilter;
        $this->characteristicGroupCollectionFactory = $characteristicGroupCollectionFactory;
        $this->hydrator = $hydrator;
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        if ($this->getRequest()->isPost()) {
            $isEnabled = (int)$this->getRequest()->getParam('is_enabled');

            $updatedItemsCount = 0;
            $collection = $this->massActionFilter->getCollection($this->characteristicGroupCollectionFactory->create());
            foreach ($collection as $characteristicGroup) {
                try {
                    $characteristicGroup = $this->characteristicGroupRepository->get(
                        $characteristicGroup->getCharacteristicGroupId()
                    );
                    $characteristicGroup = $this->hydrator->hydrate($characteristicGroup, [
                        CharacteristicGroupInterface::IS_ENABLED => $isEnabled,
                    ]);
                    $this->characteristicGroupRepository->save($characteristicGroup);
                    $updatedItemsCount++;
                } catch (CouldNotSaveException $e) {
                    $errorMessage = __('[ID: %1] ', $characteristicGroup->getCharacteristicGroupId())
                        . $e->getMessage();
                    $this->messageManager->addErrorMessage($errorMessage);
                }
            }
            $this->messageManager->addSuccessMessage(__('You updated %1 Characteristic Group(s).', $updatedItemsCount));
        } else {
            $this->messageManager->addErrorMessage(__('Wrong request.'));
        }
        return $this->resultRedirectFactory->create()->setPath('*/*');
    }
}
