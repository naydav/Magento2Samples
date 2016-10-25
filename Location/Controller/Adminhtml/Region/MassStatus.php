<?php
namespace Engine\Location\Controller\Adminhtml\Region;

use Engine\Location\Api\Data\RegionInterface;
use Engine\Location\Model\Region\DataRegionHelper;
use Engine\Location\Api\RegionRepositoryInterface;
use Engine\Location\Model\Region\ResourceModel\RegionCollectionFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
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
     * @var DataRegionHelper
     */
    private $dataRegionHelper;

    /**
     * @param Context $context
     * @param RegionRepositoryInterface $regionRepository
     * @param Filter $massActionFilter
     * @param RegionCollectionFactory $regionCollectionFactory
     * @param DataRegionHelper $dataRegionHelper
     */
    public function __construct(
        Context $context,
        RegionRepositoryInterface $regionRepository,
        Filter $massActionFilter,
        RegionCollectionFactory $regionCollectionFactory,
        DataRegionHelper $dataRegionHelper
    ) {
        parent::__construct($context);
        $this->regionRepository = $regionRepository;
        $this->massActionFilter = $massActionFilter;
        $this->regionCollectionFactory = $regionCollectionFactory;
        $this->dataRegionHelper = $dataRegionHelper;
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        $isEnabled = (int)$this->getRequest()->getParam('is_enabled');

        $updatedItemsCount = 0;
        $collection = $this->massActionFilter->getCollection($this->regionCollectionFactory->create());
        foreach ($collection as $region) {
            try {
                /** @var RegionInterface $region */
                $this->dataRegionHelper->populateWithArray($region, [RegionInterface::IS_ENABLED => $isEnabled]);
                $this->regionRepository->save($region);
                $updatedItemsCount++;
            } catch (CouldNotSaveException $e) {
                $errorMessage = __('[ID: %1] ', $region->getRegionId()) . $e->getMessage();
                $this->messageManager->addErrorMessage($errorMessage);
            }
        }
        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been updated.', $updatedItemsCount));
        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }
}
