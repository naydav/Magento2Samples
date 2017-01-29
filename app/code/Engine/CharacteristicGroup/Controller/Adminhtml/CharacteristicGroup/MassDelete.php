<?php
namespace Engine\CharacteristicGroup\Controller\Adminhtml\CharacteristicGroup;

use Engine\CharacteristicGroup\Api\Data\CharacteristicGroupInterface;
use Engine\CharacteristicGroup\Api\CharacteristicGroupRepositoryInterface;
use Engine\CharacteristicGroup\Model\CharacteristicGroup\ResourceModel\CharacteristicGroupCollectionFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Ui\Component\MassAction\Filter;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class MassDelete extends Action
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
     * @param Context $context
     * @param CharacteristicGroupRepositoryInterface $characteristicGroupRepository
     * @param Filter $massActionFilter
     * @param CharacteristicGroupCollectionFactory $characteristicGroupCollectionFactory
     */
    public function __construct(
        Context $context,
        CharacteristicGroupRepositoryInterface $characteristicGroupRepository,
        Filter $massActionFilter,
        CharacteristicGroupCollectionFactory $characteristicGroupCollectionFactory
    ) {
        parent::__construct($context);
        $this->characteristicGroupRepository = $characteristicGroupRepository;
        $this->massActionFilter = $massActionFilter;
        $this->characteristicGroupCollectionFactory = $characteristicGroupCollectionFactory;
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        if ($this->getRequest()->isPost()) {
            $deletedItemsCount = 0;
            $collection = $this->massActionFilter->getCollection($this->characteristicGroupCollectionFactory->create());
            foreach ($collection as $characteristicGroup) {
                try {
                    /** @var CharacteristicGroupInterface $characteristicGroup */
                    $this->characteristicGroupRepository->deleteById($characteristicGroup->getCharacteristicGroupId());
                    $deletedItemsCount++;
                } catch (CouldNotDeleteException $e) {
                    $errorMessage = __('[ID: %1] ', $characteristicGroup->getCharacteristicGroupId())
                        . $e->getMessage();
                    $this->messageManager->addErrorMessage($errorMessage);
                }
            }
            $this->messageManager->addSuccessMessage(__('You deleted %1 Characteristic Group(s).', $deletedItemsCount));
        } else {
            $this->messageManager->addErrorMessage(__('Wrong request.'));
        }
        return $this->resultRedirectFactory->create()->setPath('*/*');
    }
}
