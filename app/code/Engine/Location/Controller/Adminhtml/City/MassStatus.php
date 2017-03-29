<?php
namespace Engine\Location\Controller\Adminhtml\City;

use Engine\Location\Api\Data\CityInterface;
use Engine\Location\Api\CityRepositoryInterface;
use Engine\MagentoFix\Ui\Component\MassAction\Filter;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class MassStatus extends Action
{
    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Engine_Location::location_city';

    /**
     * @var CityRepositoryInterface
     */
    private $cityRepository;

    /**
     * @var Filter
     */
    private $massActionFilter;

    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @param Context $context
     * @param CityRepositoryInterface $cityRepository
     * @param Filter $massActionFilter
     * @param HydratorInterface $hydrator
     */
    public function __construct(
        Context $context,
        CityRepositoryInterface $cityRepository,
        Filter $massActionFilter,
        HydratorInterface $hydrator
    ) {
        parent::__construct($context);
        $this->cityRepository = $cityRepository;
        $this->massActionFilter = $massActionFilter;
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
            foreach ($this->massActionFilter->getIds() as $id) {
                try {
                    $city = $this->cityRepository->get($id);
                    $city = $this->hydrator->hydrate($city, [
                        CityInterface::IS_ENABLED => $isEnabled,
                    ]);
                    $this->cityRepository->save($city);
                    $updatedItemsCount++;
                } catch (CouldNotSaveException $e) {
                    $errorMessage = __('[ID: %1] ', $city->getCityId())
                        . $e->getMessage();
                    $this->messageManager->addErrorMessage($errorMessage);
                }
            }
            $this->messageManager->addSuccessMessage(__('You updated %1 City(s).', $updatedItemsCount));
        } else {
            $this->messageManager->addErrorMessage(__('Wrong request.'));
        }
        return $this->resultRedirectFactory->create()->setPath('*/*');
    }
}
