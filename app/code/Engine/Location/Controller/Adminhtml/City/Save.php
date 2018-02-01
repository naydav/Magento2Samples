<?php
declare(strict_types=1);

namespace Engine\Location\Controller\Adminhtml\City;

use Engine\Location\Api\Data\CityInterface;
use Engine\Location\Api\Data\CityInterfaceFactory;
use Engine\Location\Api\CityRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Validation\ValidationException;

/**
 * @author naydav <valeriy.nayda@gmail.com>
 */
class Save extends Action
{
    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Engine_Location::location_city';

    /**
     * @var CityInterfaceFactory
     */
    private $cityFactory;

    /**
     * @var CityRepositoryInterface
     */
    private $cityRepository;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @param Context $context
     * @param CityInterfaceFactory $cityFactory
     * @param CityRepositoryInterface $cityRepository
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        Context $context,
        CityInterfaceFactory $cityFactory,
        CityRepositoryInterface $cityRepository,
        DataObjectHelper $dataObjectHelper
    ) {
        parent::__construct($context);
        $this->cityFactory = $cityFactory;
        $this->cityRepository = $cityRepository;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * @inheritdoc
     */
    public function execute(): ResultInterface
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $requestData = $this->getRequest()->getParams();

        if (false === $this->getRequest()->isPost() || null == $requestData || empty($requestData['general'])) {
            $this->messageManager->addErrorMessage(__('Wrong request.'));
            $resultRedirect->setPath('*/*');
            return $resultRedirect;
        }

        $cityId = isset($requestData['general'][CityInterface::CITY_ID])
            ? (int)$requestData['general'][CityInterface::CITY_ID]
            : null;

        try {
            $cityId = $this->processSave($requestData, $cityId);

            $this->messageManager->addSuccessMessage(__('The City has been saved.'));
            $this->processRedirectAfterSuccessSave($resultRedirect, $cityId);
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('The City does not exist.'));
            $this->processRedirectAfterFailureSave($resultRedirect);
        } catch (ValidationException $e) {
            foreach ($e->getErrors() as $localizedError) {
                $this->messageManager->addErrorMessage($localizedError->getMessage());
            }
            $this->processRedirectAfterFailureSave($resultRedirect, $cityId);
        } catch (CouldNotSaveException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->processRedirectAfterFailureSave($resultRedirect, $cityId);
        }
        return $resultRedirect;
    }

    /**
     * @param array $requestData
     * @param int|null $cityId
     * @return int
     */
    private function processSave(array $requestData, int $cityId = null): int
    {
        if (null === $cityId) {
            /** @var $CityInterface $city */
            $city = $this->cityFactory->create();
        } else {
            $city = $this->cityRepository->get($cityId);
        }
        $this->dataObjectHelper->populateWithArray($city, $requestData['general'], CityInterface::class);

        // event is needed for populating entity with custom data from form
        $this->_eventManager->dispatch(
            'controller_action_location_city_save_entity_before',
            [
                'request' => $this->getRequest(),
                'city' => $city,
            ]
        );
        $cityId = $this->cityRepository->save($city);

        // event is needed for processing form data by several services
        $this->_eventManager->dispatch(
            'controller_action_location_city_save_entity_after',
            [
                'request' => $this->getRequest(),
                'city' => $city,
            ]
        );
        return $cityId;
    }

    /**
     * @param Redirect $resultRedirect
     * @param int $cityId
     * @return void
     */
    private function processRedirectAfterSuccessSave(Redirect $resultRedirect, int $cityId)
    {
        if ($this->getRequest()->getParam('back')) {
            $resultRedirect->setPath('*/*/edit', [
                CityInterface::CITY_ID => $cityId,
                '_current' => true,
            ]);
        } elseif ($this->getRequest()->getParam('redirect_to_new')) {
            $resultRedirect->setPath('*/*/new', [
                '_current' => true,
            ]);
        } else {
            $resultRedirect->setPath('*/*/');
        }
    }

    /**
     * @param Redirect $resultRedirect
     * @param int|null $cityId
     * @return void
     */
    private function processRedirectAfterFailureSave(Redirect $resultRedirect, int $cityId = null)
    {
        if (null === $cityId) {
            $resultRedirect->setPath('*/*/new');
        } else {
            $resultRedirect->setPath('*/*/edit', [
                CityInterface::CITY_ID => $cityId,
                '_current' => true,
            ]);
        }
    }
}
