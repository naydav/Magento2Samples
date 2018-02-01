<?php
declare(strict_types=1);

namespace Engine\Location\Controller\Adminhtml\Country;

use Engine\Location\Api\Data\CountryInterface;
use Engine\Location\Api\Data\CountryInterfaceFactory;
use Engine\Location\Api\CountryRepositoryInterface;
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
    const ADMIN_RESOURCE = 'Engine_Location::location_country';

    /**
     * @var CountryInterfaceFactory
     */
    private $countryFactory;

    /**
     * @var CountryRepositoryInterface
     */
    private $countryRepository;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @param Context $context
     * @param CountryInterfaceFactory $countryFactory
     * @param CountryRepositoryInterface $countryRepository
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        Context $context,
        CountryInterfaceFactory $countryFactory,
        CountryRepositoryInterface $countryRepository,
        DataObjectHelper $dataObjectHelper
    ) {
        parent::__construct($context);
        $this->countryFactory = $countryFactory;
        $this->countryRepository = $countryRepository;
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

        $countryId = isset($requestData['general'][CountryInterface::COUNTRY_ID])
            ? (int)$requestData['general'][CountryInterface::COUNTRY_ID]
            : null;

        try {
            $countryId = $this->processSave($requestData, $countryId);

            $this->messageManager->addSuccessMessage(__('The Country has been saved.'));
            $this->processRedirectAfterSuccessSave($resultRedirect, $countryId);
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('The Country does not exist.'));
            $this->processRedirectAfterFailureSave($resultRedirect);
        } catch (ValidationException $e) {
            foreach ($e->getErrors() as $localizedError) {
                $this->messageManager->addErrorMessage($localizedError->getMessage());
            }
            $this->processRedirectAfterFailureSave($resultRedirect, $countryId);
        } catch (CouldNotSaveException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->processRedirectAfterFailureSave($resultRedirect, $countryId);
        }
        return $resultRedirect;
    }

    /**
     * @param array $requestData
     * @param int|null $countryId
     * @return int
     */
    private function processSave(array $requestData, int $countryId = null): int
    {
        if (null === $countryId) {
            /** @var $CountryInterface $country */
            $country = $this->countryFactory->create();
        } else {
            $country = $this->countryRepository->get($countryId);
        }
        $this->dataObjectHelper->populateWithArray($country, $requestData['general'], CountryInterface::class);

        // event is needed for populating entity with custom data from form
        $this->_eventManager->dispatch(
            'controller_action_location_country_save_entity_before',
            [
                'request' => $this->getRequest(),
                'country' => $country,
            ]
        );
        $countryId = $this->countryRepository->save($country);

        // event is needed for processing form data by several services
        $this->_eventManager->dispatch(
            'controller_action_location_country_save_entity_after',
            [
                'request' => $this->getRequest(),
                'country' => $country,
            ]
        );
        return $countryId;
    }

    /**
     * @param Redirect $resultRedirect
     * @param int $countryId
     * @return void
     */
    private function processRedirectAfterSuccessSave(Redirect $resultRedirect, int $countryId)
    {
        if ($this->getRequest()->getParam('back')) {
            $resultRedirect->setPath('*/*/edit', [
                CountryInterface::COUNTRY_ID => $countryId,
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
     * @param int|null $countryId
     * @return void
     */
    private function processRedirectAfterFailureSave(Redirect $resultRedirect, int $countryId = null)
    {
        if (null === $countryId) {
            $resultRedirect->setPath('*/*/new');
        } else {
            $resultRedirect->setPath('*/*/edit', [
                CountryInterface::COUNTRY_ID => $countryId,
                '_current' => true,
            ]);
        }
    }
}
