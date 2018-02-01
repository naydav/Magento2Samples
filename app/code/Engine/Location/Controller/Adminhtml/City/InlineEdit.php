<?php
declare(strict_types=1);

namespace Engine\Location\Controller\Adminhtml\City;

use Engine\Location\Api\CityRepositoryInterface;
use Engine\Location\Api\Data\CityInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Validation\ValidationException;

/**
 * @author naydav <valeriy.nayda@gmail.com>
 */
class InlineEdit extends Action
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
     * @param Context $context
     * @param DataObjectHelper $dataObjectHelper
     * @param CityRepositoryInterface $cityRepository
     */
    public function __construct(
        Context $context,
        DataObjectHelper $dataObjectHelper,
        CityRepositoryInterface $cityRepository
    ) {
        parent::__construct($context);
        $this->dataObjectHelper = $dataObjectHelper;
        $this->cityRepository = $cityRepository;
    }

    /**
     * @inheritdoc
     */
    public function execute(): ResultInterface
    {
        $request = $this->getRequest();
        $requestData = $request->getParam('items', []);

        if (false === $request->isXmlHttpRequest() || false === $request->isPost() || empty($requestData)) {
            return $this->createJsonResult([__('Please correct the data sent.')]);
        }

        $errorMessages = [];
        foreach ($requestData as $itemData) {
            try {
                $cityId = (int)$itemData[CityInterface::CITY_ID];
                $city = $this->cityRepository->get($cityId);
                $this->dataObjectHelper->populateWithArray($city, $itemData, CityInterface::class);
                $this->cityRepository->save($city);
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
                $errorMessages[] = __('[ID: %id] %message', [
                    'id' => $cityId,
                    'message' => $e->getMessage(),
                ]);
            }
        }
        return $this->createJsonResult($errorMessages);
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
