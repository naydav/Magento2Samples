<?php
namespace Engine\Location\Controller\Adminhtml\Region;

use Engine\Location\Api\Data\RegionInterface;
use Engine\Location\Api\RegionRepositoryInterface;
use Engine\Location\Model\Region\ResourceModel\RegionCollectionFactory;
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
    const ADMIN_RESOURCE = 'Engine_Location::region';

    /**
     * @var RegionRepositoryInterface
     */
    private $regionRepository;

    /**
     * @var Filter
     */
    private $massActionFilter;

    /**
     * @var RegionCollectionFactory
     */
    private $regionCollectionFactory;

    /**
     * @param Context $context
     * @param RegionRepositoryInterface $regionRepository
     * @param Filter $massActionFilter
     * @param RegionCollectionFactory $regionCollectionFactory
     */
    public function __construct(
        Context $context,
        RegionRepositoryInterface $regionRepository,
        Filter $massActionFilter,
        RegionCollectionFactory $regionCollectionFactory
    ) {
        parent::__construct($context);
        $this->regionRepository = $regionRepository;
        $this->massActionFilter = $massActionFilter;
        $this->regionCollectionFactory = $regionCollectionFactory;
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        if ($this->getRequest()->isPost()) {
            $deletedItemsCount = 0;
            $collection = $this->massActionFilter->getCollection($this->regionCollectionFactory->create());
            foreach ($collection as $region) {
                try {
                    /** @var RegionInterface $region */
                    $this->regionRepository->deleteById($region->getRegionId());
                    $deletedItemsCount++;
                } catch (CouldNotDeleteException $e) {
                    $errorMessage = __('[ID: %1] ', $region->getRegionId()) . $e->getMessage();
                    $this->messageManager->addErrorMessage($errorMessage);
                }
            }
            $this->messageManager->addSuccessMessage(__('You deleted %1 region(s).', $deletedItemsCount));
        } else {
            $this->messageManager->addErrorMessage(__('Wrong request.'));
        }
        return $this->resultRedirectFactory->create()->setPath('*/*');
    }
}
