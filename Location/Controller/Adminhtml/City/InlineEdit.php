<?php
namespace Engine\Location\Controller\Adminhtml\City;

use Engine\Location\Api\CityRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class InlineEdit extends Action
{
    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Engine_Location::city';

    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @var CityRepositoryInterface
     */
    private $cityRepository;

    /**
     * @param Context $context
     * @param HydratorInterface $hydrator
     * @param CityRepositoryInterface $cityRepository
     */
    public function __construct(
        Context $context,
        HydratorInterface $hydrator,
        CityRepositoryInterface $cityRepository
    ) {
        parent::__construct($context);
        $this->hydrator = $hydrator;
        $this->cityRepository = $cityRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $errorMessages = [];
        $request = $this->getRequest();
        $requestData = $request->getParam('items', []);

        if ($request->isXmlHttpRequest() && $requestData) {
            foreach ($requestData as $itemData) {
                try {
                    $city = $this->cityRepository->get($itemData['city_id']);
                    $this->hydrator->hydrate($city, $itemData);
                    $this->cityRepository->save($city);
                } catch (NoSuchEntityException $e) {
                    $errorMessages[] = __('[ID: %1] The city does not exist.', $itemData['city_id']);
                } catch (CouldNotSaveException $e) {
                    $errorMessages[] = __('[ID: %1] ', $itemData['city_id']) . $e->getMessage();
                }
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
