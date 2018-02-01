<?php
declare(strict_types=1);

namespace Engine\Location\Controller\Adminhtml\Region;

use Engine\Location\Api\RegionRepositoryInterface;
use Engine\Location\Api\Data\RegionInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @author naydav <valeriy.nayda@gmail.com>
 */
class Delete extends Action
{
    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Engine_Location::location_region';

    /**
     * @var RegionRepositoryInterface
     */
    private $regionRepository;

    /**
     * @param Context $context
     * @param RegionRepositoryInterface $regionRepository
     */
    public function __construct(
        Context $context,
        RegionRepositoryInterface $regionRepository
    ) {
        parent::__construct($context);
        $this->regionRepository = $regionRepository;
    }

    /**
     * @inheritdoc
     */
    public function execute(): ResultInterface
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        $regionId = $this->getRequest()->getPost(RegionInterface::REGION_ID);
        if (null === $regionId) {
            $this->messageManager->addErrorMessage(__('Wrong request.'));
            return $resultRedirect->setPath('*/*');
        }

        try {
            $regionId = (int)$regionId;
            $this->regionRepository->deleteById($regionId);
            $this->messageManager->addSuccessMessage(__('The Region has been deleted.'));
            $resultRedirect->setPath('*/*');
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(
                __('Region with id "%id" does not exist.', ['id' => $regionId])
            );
            $resultRedirect->setPath('*/*');
        } catch (CouldNotDeleteException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $resultRedirect->setPath('*/*/edit', [
            RegionInterface::REGION_ID => $regionId,
                '_current' => true,
            ]);
        }
        return $resultRedirect;
    }
}
