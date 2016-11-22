<?php
namespace Engine\Location\Controller\Adminhtml\City;

use Engine\Location\Api\Data\CityInterface;
use Engine\Location\Api\CityRepositoryInterface;
use Engine\Location\Model\City\ResourceModel\CityCollectionFactory;
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
     * @param Context $context
     * @param CityRepositoryInterface $cityRepository
     * @param Filter $massActionFilter
     * @param CityCollectionFactory $cityCollectionFactory
     */
    public function __construct(
        Context $context,
        CityRepositoryInterface $cityRepository,
        Filter $massActionFilter,
        CityCollectionFactory $cityCollectionFactory
    ) {
        parent::__construct($context);
        $this->cityRepository = $cityRepository;
        $this->massActionFilter = $massActionFilter;
        $this->cityCollectionFactory = $cityCollectionFactory;
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        if ($this->getRequest()->isPost()) {
            $deletedItemsCount = 0;
            $collection = $this->massActionFilter->getCollection($this->cityCollectionFactory->create());
            foreach ($collection as $city) {
                try {
                    /** @var CityInterface $city */
                    $this->cityRepository->deleteById($city->getCityId());
                    $deletedItemsCount++;
                } catch (CouldNotDeleteException $e) {
                    $errorMessage = __('[ID: %1] ', $city->getCityId()) . $e->getMessage();
                    $this->messageManager->addErrorMessage($errorMessage);
                }
            }
            $this->messageManager->addSuccessMessage(__('You deleted %1 city(s).', $deletedItemsCount));
        } else {
            $this->messageManager->addErrorMessage(__('Wrong request.'));
        }
        return $this->resultRedirectFactory->create()->setPath('*/*/index');
    }
}
