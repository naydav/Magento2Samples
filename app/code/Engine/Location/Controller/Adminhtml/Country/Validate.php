<?php
declare(strict_types=1);

namespace Engine\Location\Controller\Adminhtml\Country;

use Engine\Location\Api\Data\CountryInterface;
use Engine\Location\Api\Data\CountryInterfaceFactory;
use Engine\Location\Api\CountryRepositoryInterface;
use Engine\Location\Model\Country\Validator\CountryValidatorInterface;
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
     * @var CountryValidatorInterface
     */
    private $countryValidator;

    /**
     * @param Context $context
     * @param CountryInterfaceFactory $countryFactory
     * @param CountryRepositoryInterface $countryRepository
     * @param DataObjectHelper $dataObjectHelper
     * @param CountryValidatorInterface $countryValidator
     */
    public function __construct(
        Context $context,
        CountryInterfaceFactory $countryFactory,
        CountryRepositoryInterface $countryRepository,
        DataObjectHelper $dataObjectHelper,
        CountryValidatorInterface $countryValidator
    ) {
        parent::__construct($context);
        $this->countryFactory = $countryFactory;
        $this->countryRepository = $countryRepository;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->countryValidator = $countryValidator;
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

        $countryId = isset($requestData['general'][CountryInterface::COUNTRY_ID])
            ? (int)$requestData['general'][CountryInterface::COUNTRY_ID]
            : null;

        try {
            $errorMessages = $this->processValidate($requestData, $countryId);
        } catch (NoSuchEntityException $e) {
            $errorMessages[] = __('The Country does not exist.');
        }
        return $this->createJsonResult($errorMessages);
    }

    /**
     * @param array $requestData
     * @param int|null $countryId
     * @return array
     */
    private function processValidate(array $requestData, int $countryId = null): array
    {
        if (null === $countryId) {
            /** @var CountryInterface $country */
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
        $validationResult = $this->countryValidator->validate($country);
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
