<?php
namespace Engine\CharacteristicGroup\Controller\Adminhtml\CharacteristicGroup;

use Engine\CharacteristicGroup\Api\CharacteristicGroupRepositoryInterface;
use Engine\CharacteristicGroup\Api\Data\CharacteristicGroupInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class Edit extends Action
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
     * @param Context $context
     * @param CharacteristicGroupRepositoryInterface $characteristicGroupRepository
     */
    public function __construct(
        Context $context,
        CharacteristicGroupRepositoryInterface $characteristicGroupRepository
    ) {
        parent::__construct($context);
        $this->characteristicGroupRepository = $characteristicGroupRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $characteristicGroupId = $this->getRequest()->getParam(CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID);
        try {
            $characteristicGroup = $this->characteristicGroupRepository->get($characteristicGroupId);

            /** @var Page $result */
            $result = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
            $result->setActiveMenu('Engine_CharacteristicGroup::characteristic_group_characteristic_group')
                ->addBreadcrumb(__('Edit Characteristic Group'), __('Edit Characteristic Group'));
            $result->getConfig()
                ->getTitle()
                ->prepend(__('Edit Characteristic Group: %1', $characteristicGroup->getTitle()));
        } catch (NoSuchEntityException $e) {
            /** @var Redirect $result */
            $result = $this->resultRedirectFactory->create();
            $this->messageManager->addErrorMessage(
                __('Characteristic Group with id "%1" does not exist.', $characteristicGroupId)
            );
            $result->setPath('*/*');
        }
        return $result;
    }
}
