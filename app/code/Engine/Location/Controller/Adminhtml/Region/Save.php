<?php
declare(strict_types=1);

namespace Engine\Location\Controller\Adminhtml\Region;

use Engine\Location\Api\Data\RegionInterface;
use Engine\Location\Api\Data\RegionInterfaceFactory;
use Engine\Location\Api\RegionRepositoryInterface;
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
    const ADMIN_RESOURCE = 'Engine_Location::location_region';

    /**
     * @var RegionInterfaceFactory
     */
    private $regionFactory;

    /**
     * @var RegionRepositoryInterface
     */
    private $regionRepository;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @param Context $context
     * @param RegionInterfaceFactory $regionFactory
     * @param RegionRepositoryInterface $regionRepository
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        Context $context,
        RegionInterfaceFactory $regionFactory,
        RegionRepositoryInterface $regionRepository,
        DataObjectHelper $dataObjectHelper
    ) {
        parent::__construct($context);
        $this->regionFactory = $regionFactory;
        $this->regionRepository = $regionRepository;
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

        $regionId = isset($requestData['general'][RegionInterface::REGION_ID])
            ? (int)$requestData['general'][RegionInterface::REGION_ID]
            : null;

        try {
            $regionId = $this->processSave($requestData, $regionId);

            $this->messageManager->addSuccessMessage(__('The Region has been saved.'));
            $this->processRedirectAfterSuccessSave($resultRedirect, $regionId);
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('The Region does not exist.'));
            $this->processRedirectAfterFailureSave($resultRedirect);
        } catch (ValidationException $e) {
            foreach ($e->getErrors() as $localizedError) {
                $this->messageManager->addErrorMessage($localizedError->getMessage());
            }
            $this->processRedirectAfterFailureSave($resultRedirect, $regionId);
        } catch (CouldNotSaveException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->processRedirectAfterFailureSave($resultRedirect, $regionId);
        }
        return $resultRedirect;
    }

    /**
     * @param array $requestData
     * @param int|null $regionId
     * @return int
     */
    private function processSave(array $requestData, int $regionId = null): int
    {
        if (null === $regionId) {
            /** @var $RegionInterface $region */
            $region = $this->regionFactory->create();
        } else {
            $region = $this->regionRepository->get($regionId);
        }
        $this->dataObjectHelper->populateWithArray($region, $requestData['general'], RegionInterface::class);

        // event is needed for populating entity with custom data from form
        $this->_eventManager->dispatch(
            'controller_action_location_region_save_entity_before',
            [
                'request' => $this->getRequest(),
                'region' => $region,
            ]
        );
        $regionId = $this->regionRepository->save($region);

        // event is needed for processing form data by several services
        $this->_eventManager->dispatch(
            'controller_action_location_region_save_entity_after',
            [
                'request' => $this->getRequest(),
                'region' => $region,
            ]
        );
        return $regionId;
    }

    /**
     * @param Redirect $resultRedirect
     * @param int $regionId
     * @return void
     */
    private function processRedirectAfterSuccessSave(Redirect $resultRedirect, int $regionId)
    {
        if ($this->getRequest()->getParam('back')) {
            $resultRedirect->setPath('*/*/edit', [
                RegionInterface::REGION_ID => $regionId,
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
     * @param int|null $regionId
     * @return void
     */
    private function processRedirectAfterFailureSave(Redirect $resultRedirect, int $regionId = null)
    {
        if (null === $regionId) {
            $resultRedirect->setPath('*/*/new');
        } else {
            $resultRedirect->setPath('*/*/edit', [
                RegionInterface::REGION_ID => $regionId,
                '_current' => true,
            ]);
        }
    }
}
