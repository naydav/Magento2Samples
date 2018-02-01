<?php
declare(strict_types=1);

namespace Engine\Location\Controller\Adminhtml\Region;

use Engine\Location\Api\Data\RegionInterface;
use Engine\Location\Api\Data\RegionInterfaceFactory;
use Engine\Location\Api\RegionRepositoryInterface;
use Engine\Location\Model\Region\Validator\RegionValidatorInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @author naydav <valeriy.nayda@gmail.com>
 */
class Validate extends Action
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
     * @var RegionValidatorInterface
     */
    private $regionValidator;

    /**
     * @param Context $context
     * @param RegionInterfaceFactory $regionFactory
     * @param RegionRepositoryInterface $regionRepository
     * @param DataObjectHelper $dataObjectHelper
     * @param RegionValidatorInterface $regionValidator
     */
    public function __construct(
        Context $context,
        RegionInterfaceFactory $regionFactory,
        RegionRepositoryInterface $regionRepository,
        DataObjectHelper $dataObjectHelper,
        RegionValidatorInterface $regionValidator
    ) {
        parent::__construct($context);
        $this->regionFactory = $regionFactory;
        $this->regionRepository = $regionRepository;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->regionValidator = $regionValidator;
    }

    /**
     * @inheritdoc
     */
    public function execute(): ResultInterface
    {
        $request = $this->getRequest();
        $requestData = $request->getParams();

        if (false === $request->isXmlHttpRequest() || false === $request->isPost() || empty($requestData['general'])) {
            return $this->createJsonResult([__('Please correct the data sent.')]);
        }

        $regionId = isset($requestData['general'][RegionInterface::REGION_ID])
            ? (int)$requestData['general'][RegionInterface::REGION_ID]
            : null;

        try {
            $errorMessages = $this->processValidate($requestData, $regionId);
        } catch (NoSuchEntityException $e) {
            $errorMessages[] = __('The Region does not exist.');
        }
        return $this->createJsonResult($errorMessages);
    }

    /**
     * @param array $requestData
     * @param int|null $regionId
     * @return array
     */
    private function processValidate(array $requestData, int $regionId = null): array
    {
        if (null === $regionId) {
            /** @var RegionInterface $region */
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
        $validationResult = $this->regionValidator->validate($region);
        $errorMessages = $validationResult->getErrors();
        return $errorMessages;
    }

    /**
     * @param array $errorMessages
     * @return Json
     */
    private function createJsonResult(array $errorMessages): Json
    {
        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData([
            'messages' => $errorMessages,
            'error' => count($errorMessages),
        ]);
        return $resultJson;
    }
}
