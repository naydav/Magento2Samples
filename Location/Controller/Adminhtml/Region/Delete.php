<?php
namespace Engine\Location\Controller\Adminhtml\Region;

use Engine\Location\Api\RegionRepositoryInterface;
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
    const ADMIN_RESOURCE = 'Engine_Location::region';

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
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        $regionId = $this->getRequest()->getPost('region_id');
        if ($this->getRequest()->isPost() && null !== $regionId) {
            try {
                $this->regionRepository->deleteById($regionId);
                $this->messageManager->addSuccessMessage(__('The region has been deleted.'));
                $resultRedirect->setPath('*/*/index');
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('Region with id "%1" does not exist.', $regionId));
                $resultRedirect->setPath('*/*/index');
            } catch (CouldNotDeleteException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $resultRedirect->setPath('*/*/edit', ['region_id' => $regionId, '_current' => true]);
            }
        } else {
            $this->messageManager->addErrorMessage(__('Wrong request.'));
            $resultRedirect->setPath('*/*/index');
        }
        return $resultRedirect;
    }
}
