<?php
declare(strict_types=1);

namespace Engine\Location\Controller\Adminhtml\City;

use Engine\Location\Api\Data\CityInterface;
use Engine\Location\Api\Data\CityInterfaceFactory;
use Engine\Location\Api\CityRepositoryInterface;
use Engine\Location\Model\City\Validator\CityValidatorInterface;
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
     * @var CityValidatorInterface
     */
    private $cityValidator;

    /**
     * @param Context $context
     * @param CityInterfaceFactory $cityFactory
     * @param CityRepositoryInterface $cityRepository
     * @param DataObjectHelper $dataObjectHelper
     * @param CityValidatorInterface $cityValidator
     */
    public function __construct(
        Context $context,
        CityInterfaceFactory $cityFactory,
        CityRepositoryInterface $cityRepository,
        DataObjectHelper $dataObjectHelper,
        CityValidatorInterface $cityValidator
    ) {
        parent::__construct($context);
        $this->cityFactory = $cityFactory;
        $this->cityRepository = $cityRepository;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->cityValidator = $cityValidator;
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

        $cityId = isset($requestData['general'][CityInterface::CITY_ID])
            ? (int)$requestData['general'][CityInterface::CITY_ID]
            : null;

        try {
            $errorMessages = $this->processValidate($requestData, $cityId);
        } catch (NoSuchEntityException $e) {
            $errorMessages[] = __('The City does not exist.');
        }
        return $this->createJsonResult($errorMessages);
    }

    /**
     * @param array $requestData
     * @param int|null $cityId
     * @return array
     */
    private function processValidate(array $requestData, int $cityId = null): array
    {
        if (null === $cityId) {
            /** @var CityInterface $city */
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
        $validationResult = $this->cityValidator->validate($city);
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
