<?php
namespace Engine\Location\Controller\Adminhtml\Region;

use Engine\Location\Api\Data\RegionInterface;
use Engine\Location\Api\Data\RegionInterfaceFactory;
use Engine\Location\Api\RegionRepositoryInterface;
use Engine\Location\Model\RegionCityRelationProcessor;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\Exception\CouldNotSaveException;
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
     * @var RegionCityRelationProcessor
     */
    private $regionCityRelationProcessor;

    /**
     * @param Context $context
     * @param RegionInterfaceFactory $regionFactory
     * @param RegionRepositoryInterface $regionRepository
     * @param HydratorInterface $hydrator
     * @param RegionCityRelationProcessor $regionCityRelationProcessor
     */
    public function __construct(
        Context $context,
        RegionInterfaceFactory $regionFactory,
        RegionRepositoryInterface $regionRepository,
        HydratorInterface $hydrator,
        RegionCityRelationProcessor $regionCityRelationProcessor
    ) {
        parent::__construct($context);
        $this->regionFactory = $regionFactory;
        $this->regionRepository = $regionRepository;
        $this->hydrator = $hydrator;
        $this->regionCityRelationProcessor = $regionCityRelationProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $regionRequestData = $this->getRequest()->getParam('general');
        if ($this->getRequest()->isPost() && $regionRequestData) {
            try {
                $useDefaults = $this->getRequest()->getParam('use_default', []);
                if ($useDefaults) {
                    foreach ($useDefaults as $field => $useDefaultState) {
                        if (1 === (int)$useDefaultState) {
                            $regionRequestData[$field] = null;
                        }
                    }
                }
                $regionId = !empty($regionRequestData['region_id']) ? $regionRequestData['region_id'] : null;

                if ($regionId) {
                    $region = $this->regionRepository->get($regionId);
                } else {
                    /** @var RegionInterface $region */
                    $region = $this->regionFactory->create();
                }
                $this->hydrator->hydrate($region, $regionRequestData);
                $this->regionRepository->save($region);

                $citiesRequestData = $this->getRequest()->getParam('cities', []);
                if ($citiesRequestData) {
                    $this->regionCityRelationProcessor->process(
                        $region->getRegionId(),
                        $citiesRequestData['assigned_cities']
                    );
                }

                $this->messageManager->addSuccessMessage(__('The Region has been saved.'));
                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath('*/*/edit', ['region_id' => $region->getRegionId(), '_current' => true]);
                } else {
                    $resultRedirect->setPath('*/*/');
                }
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('The region does not exist.'));
                $resultRedirect->setPath('*/*/');
            } catch (CouldNotSaveException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                if ($regionId) {
                    $resultRedirect->setPath('*/*/edit', ['region_id' => $regionId, '_current' => true]);
                } else {
                    $resultRedirect->setPath('*/*/');
                }
            }
        } else {
            $this->messageManager->addErrorMessage(__('Wrong request.'));
            $resultRedirect->setPath('*/*/index');
        }
        return $resultRedirect;
    }
}
