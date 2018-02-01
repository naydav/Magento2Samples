<?php
declare(strict_types=1);

namespace Engine\Location\Controller\Adminhtml\Region;

use Engine\Location\Api\RegionRepositoryInterface;
use Engine\Location\Api\Data\RegionInterface;
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
    const ADMIN_RESOURCE = 'Engine_Location::location_region';

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var RegionRepositoryInterface
     */
    private $regionRepository;

    /**
     * @param Context $context
     * @param DataObjectHelper $dataObjectHelper
     * @param RegionRepositoryInterface $regionRepository
     */
    public function __construct(
        Context $context,
        DataObjectHelper $dataObjectHelper,
        RegionRepositoryInterface $regionRepository
    ) {
        parent::__construct($context);
        $this->dataObjectHelper = $dataObjectHelper;
        $this->regionRepository = $regionRepository;
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
                $regionId = (int)$itemData[RegionInterface::REGION_ID];
                $region = $this->regionRepository->get($regionId);
                $this->dataObjectHelper->populateWithArray($region, $itemData, RegionInterface::class);
                $this->regionRepository->save($region);
            } catch (NoSuchEntityException $e) {
                $errorMessages[] = __(
                    '[ID: %id] The Region does not exist.',
                    ['id' => $regionId]
                );
            } catch (ValidationException $e) {
                foreach ($e->getErrors() as $localizedError) {
                    $errorMessages[] = __('[ID: %id] %message', [
                        'id' => $regionId,
                        'message' => $localizedError->getMessage(),
                    ]);
                }
            } catch (CouldNotSaveException $e) {
                $errorMessages[] = __('[ID: %id] %message', [
                    'id' => $regionId,
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
