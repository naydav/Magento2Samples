<?php
namespace Engine\Location\Controller\Adminhtml\City;

use Engine\Location\Api\Data\CityInterface;
use Engine\Location\Api\Data\CityInterfaceFactory;
use Engine\Location\Api\CityRepositoryInterface;
use Engine\Location\Model\City\CityValidatorInterface;
use Engine\Validation\Exception\ValidatorException;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\EntityManager\HydratorInterface;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
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
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @var CityValidatorInterface
     */
    private $cityValidator;

    /**
     * @param Context $context
     * @param CityInterfaceFactory $cityFactory
     * @param CityRepositoryInterface $cityRepository
     * @param HydratorInterface $hydrator
     * @param CityValidatorInterface $cityValidator
     */
    public function __construct(
        Context $context,
        CityInterfaceFactory $cityFactory,
        CityRepositoryInterface $cityRepository,
        HydratorInterface $hydrator,
        CityValidatorInterface $cityValidator
    ) {
        parent::__construct($context);
        $this->cityFactory = $cityFactory;
        $this->cityRepository = $cityRepository;
        $this->hydrator = $hydrator;
        $this->cityValidator = $cityValidator;
    }

    /**
     * @return Json
     */
    public function execute()
    {
        $errorMessages = [];
        $request = $this->getRequest();
        $requestData = $this->getRequest()->getParam('general');

        if ($request->isXmlHttpRequest() && $this->getRequest()->isPost() && $requestData) {
            $cityId = !empty($requestData[CityInterface::CITY_ID])
                ? $requestData[CityInterface::CITY_ID] : null;

            if ($cityId) {
                $city = $this->cityRepository->get($cityId);
            } else {
                /** @var CityInterface $city */
                $city = $this->cityFactory->create();
            }
            $city = $this->hydrator->hydrate($city, $requestData);

            try {
                $this->cityValidator->validate($city);
            } catch (ValidatorException $e) {
                $errorMessages = $e->getErrors();
            }
        } else {
            $errorMessages[] = __('Please correct the data sent.');
        }

        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData([
            'messages' => $errorMessages,
            'error' => count($errorMessages),
        ]);
        return $resultJson;
    }
}
