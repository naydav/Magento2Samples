<?php
namespace Engine\CharacteristicGroup\Controller\Adminhtml\CharacteristicGroup;

use Engine\CharacteristicGroup\Api\CharacteristicGroupRepositoryInterface;
use Engine\CharacteristicGroup\Api\Data\CharacteristicGroupInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class Delete extends Action
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
        $resultRedirect = $this->resultRedirectFactory->create();

        $characteristicGroupId = $this->getRequest()->getPost(CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID);
        if ($this->getRequest()->isPost() && null !== $characteristicGroupId) {
            try {
                $this->characteristicGroupRepository->deleteById($characteristicGroupId);
                $this->messageManager->addSuccessMessage(__('The Characteristic Group has been deleted.'));
                $resultRedirect->setPath('*/*');
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(
                    __('Characteristic Group with id "%1" does not exist.', $characteristicGroupId)
                );
                $resultRedirect->setPath('*/*');
            } catch (CouldNotDeleteException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $resultRedirect->setPath('*/*/edit', [
                    CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID => $characteristicGroupId,
                    '_current' => true,
                ]);
            }
        } else {
            $this->messageManager->addErrorMessage(__('Wrong request.'));
            $resultRedirect->setPath('*/*');
        }
        return $resultRedirect;
    }
}
