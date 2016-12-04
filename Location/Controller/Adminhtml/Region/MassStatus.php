<?php
namespace Engine\Location\Controller\Adminhtml\Region;

use Engine\Location\Api\Data\RegionInterface;
use Engine\Location\Api\RegionRepositoryInterface;
use Engine\Location\Model\Region\ResourceModel\RegionCollectionFactory;
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
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @param Context $context
     * @param RegionRepositoryInterface $regionRepository
     * @param Filter $massActionFilter
     * @param RegionCollectionFactory $regionCollectionFactory
     * @param HydratorInterface $hydrator
     */
    public function __construct(
        Context $context,
        RegionRepositoryInterface $regionRepository,
        Filter $massActionFilter,
        RegionCollectionFactory $regionCollectionFactory,
        HydratorInterface $hydrator
    ) {
        parent::__construct($context);
        $this->regionRepository = $regionRepository;
        $this->massActionFilter = $massActionFilter;
        $this->regionCollectionFactory = $regionCollectionFactory;
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
            $collection = $this->massActionFilter->getCollection($this->regionCollectionFactory->create());
            foreach ($collection as $region) {
                try {
                    /** @var RegionInterface $region */
                    $this->hydrator->hydrate($region, [RegionInterface::IS_ENABLED => $isEnabled]);
                    $this->regionRepository->save($region);
                    $updatedItemsCount++;
                } catch (CouldNotSaveException $e) {
                    $errorMessage = __('[ID: %1] ', $region->getRegionId()) . $e->getMessage();
                    $this->messageManager->addErrorMessage($errorMessage);
                }
            }
            $this->messageManager->addSuccessMessage(__('You updated %1 region(s).', $updatedItemsCount));
        } else {
            $this->messageManager->addErrorMessage(__('Wrong request.'));
        }
        return $this->resultRedirectFactory->create()->setPath('*/*/index');
    }
}
