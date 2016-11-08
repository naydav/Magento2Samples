<?php
namespace Engine\Location\Controller\Adminhtml\Region;

use Engine\Location\Api\Data\RegionInterface;
use Engine\Location\Api\Data\RegionInterfaceFactory;
use Engine\Location\Api\RegionRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class Save extends Action
{
    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Engine_Location::region';

    /**
     * @var RegionInterfaceFactory
     */
    private $regionFactory;

    /**
     * @var RegionRepositoryInterface
     */
    private $regionRepository;

    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @param Context $context
     * @param RegionInterfaceFactory $regionFactory
     * @param RegionRepositoryInterface $regionRepository
     * @param HydratorInterface $hydrator
     */
    public function __construct(
        Context $context,
        RegionInterfaceFactory $regionFactory,
        RegionRepositoryInterface $regionRepository,
        HydratorInterface $hydrator
    ) {
        parent::__construct($context);
        $this->regionFactory = $regionFactory;
        $this->regionRepository = $regionRepository;
        $this->hydrator = $hydrator;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $requestData = $this->getRequest()->getParam('general');
            if (!$requestData) {
                throw new LocalizedException(__('Please correct the data sent.'));
            }
            $regionId = !empty($requestData['region_id']) ? $requestData['region_id'] : null;

            if ($regionId) {
                $region = $this->regionRepository->get($regionId);
            } else {
                /** @var RegionInterface $region */
                $region = $this->regionFactory->create();
            }
            $this->hydrator->hydrate($region, $requestData);
            $this->regionRepository->save($region);

            $this->messageManager->addSuccessMessage(__('The Region has been saved'));
            if ($this->getRequest()->getParam('back')) {
                $resultRedirect->setPath('*/*/edit', ['region_id' => $region->getRegionId(), '_current' => true]);
            } else {
                $resultRedirect->setPath('*/*/');
            }
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('The region no exists.'));
            $resultRedirect->setPath('*/*/');
        } catch (CouldNotSaveException $e) {
            echo $e->getMessage();
            $this->messageManager->addErrorMessage($e->getMessage());
            if ($regionId) {
                $resultRedirect->setPath('*/*/edit', ['region_id' => $regionId, '_current' => true]);
            } else {
                $resultRedirect->setPath('*/*/');
            }
        }
        return $resultRedirect;
    }
}
