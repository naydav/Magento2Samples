<?php
declare(strict_types=1);

namespace Engine\Location\Controller\Adminhtml\Country;

use Engine\Location\Api\CountryRepositoryInterface;
use Engine\Location\Api\Data\CountryInterface;
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
    const ADMIN_RESOURCE = 'Engine_Location::location_country';

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var CountryRepositoryInterface
     */
    private $countryRepository;

    /**
     * @param Context $context
     * @param DataObjectHelper $dataObjectHelper
     * @param CountryRepositoryInterface $countryRepository
     */
    public function __construct(
        Context $context,
        DataObjectHelper $dataObjectHelper,
        CountryRepositoryInterface $countryRepository
    ) {
        parent::__construct($context);
        $this->dataObjectHelper = $dataObjectHelper;
        $this->countryRepository = $countryRepository;
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
                $countryId = (int)$itemData[CountryInterface::COUNTRY_ID];
                $country = $this->countryRepository->get($countryId);
                $this->dataObjectHelper->populateWithArray($country, $itemData, CountryInterface::class);
                $this->countryRepository->save($country);
            } catch (NoSuchEntityException $e) {
                $errorMessages[] = __(
                    '[ID: %id] The Country does not exist.',
                    ['id' => $countryId]
                );
            } catch (ValidationException $e) {
                foreach ($e->getErrors() as $localizedError) {
                    $errorMessages[] = __('[ID: %id] %message', [
                        'id' => $countryId,
                        'message' => $localizedError->getMessage(),
                    ]);
                }
            } catch (CouldNotSaveException $e) {
                $errorMessages[] = __('[ID: %id] %message', [
                    'id' => $countryId,
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
