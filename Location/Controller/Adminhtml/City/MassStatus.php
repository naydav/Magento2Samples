<?php
namespace Engine\Location\Controller\Adminhtml\City;

use Engine\Location\Api\Data\CityInterface;
use Engine\Location\Api\CityRepositoryInterface;
use Engine\Location\Model\City\ResourceModel\CityCollectionFactory;
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
    const ADMIN_RESOURCE = 'Engine_Location::city';

    /**
     * @var CityRepositoryInterface
     */
    private $cityRepository;

    /**
     * @var Filter
     */
    private $massActionFilter;

    /**
     * @var CityCollectionFactory
     */
    private $cityCollectionFactory;

    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @param Context $context
     * @param CityRepositoryInterface $cityRepository
     * @param Filter $massActionFilter
     * @param CityCollectionFactory $cityCollectionFactory
     * @param HydratorInterface $hydrator
     */
    public function __construct(
        Context $context,
        CityRepositoryInterface $cityRepository,
        Filter $massActionFilter,
        CityCollectionFactory $cityCollectionFactory,
        HydratorInterface $hydrator
    ) {
        parent::__construct($context);
        $this->cityRepository = $cityRepository;
        $this->massActionFilter = $massActionFilter;
        $this->cityCollectionFactory = $cityCollectionFactory;
        $this->hydrator = $hydrator;
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        $isEnabled = (int)$this->getRequest()->getParam('is_enabled');

        $updatedItemsCount = 0;
        $collection = $this->massActionFilter->getCollection($this->cityCollectionFactory->create());
        foreach ($collection as $city) {
            try {
                /** @var CityInterface $city */
                $this->hydrator->hydrate($city, [CityInterface::IS_ENABLED => $isEnabled]);
                $this->cityRepository->save($city);
                $updatedItemsCount++;
            } catch (CouldNotSaveException $e) {
                $errorMessage = __('[ID: %1] ', $city->getCityId()) . $e->getMessage();
                $this->messageManager->addErrorMessage($errorMessage);
            }
        }
        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been updated.', $updatedItemsCount));
        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }
}
