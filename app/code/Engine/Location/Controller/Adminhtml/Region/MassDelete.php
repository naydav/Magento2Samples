<?php
declare(strict_types=1);

namespace Engine\Location\Controller\Adminhtml\Region;

use Engine\Location\Api\RegionRepositoryInterface;
use Engine\Magento\Ui\Component\MassAction\Filter;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @author naydav <valeriy.nayda@gmail.com>
 */
class MassDelete extends Action
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
     * @var Filter
     */
    private $massActionFilter;

    /**
     * @param Context $context
     * @param RegionRepositoryInterface $regionRepository
     * @param Filter $massActionFilter
     */
    public function __construct(
        Context $context,
        RegionRepositoryInterface $regionRepository,
        Filter $massActionFilter
    ) {
        parent::__construct($context);
        $this->regionRepository = $regionRepository;
        $this->massActionFilter = $massActionFilter;
    }

    /**
     * @inheritdoc
     */
    public function execute(): ResultInterface
    {
        if (false === $this->getRequest()->isPost()) {
            $this->messageManager->addErrorMessage(__('Wrong request.'));
            return $this->resultRedirectFactory->create()->setPath('*/*');
        }

        $deletedItemsCount = 0;
        foreach ($this->massActionFilter->getIds() as $regionId) {
            try {
                $regionId = (int)$regionId;
                $this->regionRepository->deleteById($regionId);
                $deletedItemsCount++;
            } catch (NoSuchEntityException $e) {
                $errorMessage = __('[ID: %id] %message', ['id' => $regionId, 'message' => $e->getMessage()]);
                $this->messageManager->addErrorMessage($errorMessage);
            } catch (CouldNotDeleteException $e) {
                $errorMessage = __('[ID: %id] %message', ['id' => $regionId, 'message' => $e->getMessage()]);
                $this->messageManager->addErrorMessage($errorMessage);
            }
        }

        $this->messageManager->addSuccessMessage(__('You deleted %count Region(s).', ['count' => $deletedItemsCount]));
        return $this->resultRedirectFactory->create()->setPath('*/*');
    }
}
