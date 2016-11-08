<?php
namespace Engine\Location\Controller\Adminhtml\Region;

use Engine\Location\Api\RegionRepositoryInterface;
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
    const ADMIN_RESOURCE = 'Engine_Location::region';

    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @var RegionRepositoryInterface
     */
    private $regionRepository;

    /**
     * @param Context $context
     * @param HydratorInterface $hydrator
     * @param RegionRepositoryInterface $regionRepository
     */
    public function __construct(
        Context $context,
        HydratorInterface $hydrator,
        RegionRepositoryInterface $regionRepository
    ) {
        parent::__construct($context);
        $this->hydrator = $hydrator;
        $this->regionRepository = $regionRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $errorMessages = [];
        $requestData = $this->getRequest()->getParam('items', []);

        if ($this->getRequest()->getParam('isAjax') && $requestData) {
            foreach ($requestData as $itemData) {
                try {
                    $region = $this->regionRepository->get($itemData['region_id']);
                    $this->hydrator->hydrate($region, $itemData);
                    $this->regionRepository->save($region);
                } catch (NoSuchEntityException $e) {
                    $errorMessages[] = __('[ID: %1] The region no exists.', $itemData['region_id']);
                } catch (CouldNotSaveException $e) {
                    $errorMessages[] = __('[ID: %1] ', $itemData['region_id']) . $e->getMessage();
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
