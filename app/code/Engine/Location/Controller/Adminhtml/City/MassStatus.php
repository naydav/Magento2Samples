<?php
declare(strict_types=1);

namespace Engine\Location\Controller\Adminhtml\City;

use Engine\Location\Api\Data\CityInterface;
use Engine\Location\Api\CityRepositoryInterface;
use Engine\Magento\Ui\Component\MassAction\Filter;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Validation\ValidationException;

/**
 * @author naydav <valeriy.nayda@gmail.com>
 */
class MassStatus extends Action
{
    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Engine_Location::location_city';

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var CityRepositoryInterface
     */
    private $cityRepository;

    /**
     * @var Filter
     */
    private $massActionFilter;

    /**
     * @param Context $context
     * @param DataObjectHelper $dataObjectHelper
     * @param CityRepositoryInterface $cityRepository
     * @param Filter $massActionFilter
     */
    public function __construct(
        Context $context,
        DataObjectHelper $dataObjectHelper,
        CityRepositoryInterface $cityRepository,
        Filter $massActionFilter
    ) {
        parent::__construct($context);
        $this->dataObjectHelper = $dataObjectHelper;
        $this->cityRepository = $cityRepository;
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

        $enabled = (int)$this->getRequest()->getParam(CityInterface::ENABLED);

        $updatedItemsCount = 0;
        foreach ($this->massActionFilter->getIds() as $cityId) {
            try {
                $cityId = (int)$cityId;
                $city = $this->cityRepository->get($cityId);
                $city->setEnabled($enabled);
                $this->cityRepository->save($city);
                $updatedItemsCount++;
            } catch (NoSuchEntityException $e) {
                $errorMessages[] = __(
                    '[ID: %id] The City does not exist.',
                    ['id' => $cityId]
                );
            } catch (ValidationException $e) {
                foreach ($e->getErrors() as $localizedError) {
                    $errorMessages[] = __('[ID: %id] %message', [
                        'id' => $cityId,
                        'message' => $localizedError->getMessage(),
                    ]);
                }
            } catch (CouldNotSaveException $e) {
                $errorMessage = __('[ID: %id] %message', ['id' => $cityId, 'message' => $e->getMessage()]);
                $this->messageManager->addErrorMessage($errorMessage);
            }
        }

        $this->messageManager->addSuccessMessage(__('You updated %count City(s).', [
            'count' => $updatedItemsCount,
        ]));
        return $this->resultRedirectFactory->create()->setPath('*/*');
    }
}
