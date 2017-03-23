<?php
namespace Engine\Location\Controller\Adminhtml\Region;

use Engine\Location\Api\Data\RegionInterface;
use Engine\Location\Api\Data\RegionInterfaceFactory;
use Engine\Location\Api\RegionRepositoryInterface;
use Engine\Location\Model\RegionCityRelationProcessor;
use Engine\MagentoFix\Exception\ValidatorException;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class Save extends Action
{
    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Engine_Location::location_region';

    /**
     * Registry region_id key
     */
    const REGISTRY_REGION_ID_KEY = 'region_id';

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
     * @var Registry
     */
    private $registry;

    /**
     * @var RegionCityRelationProcessor
     */
    private $regionCityRelationProcessor;

    /**
     * @param Context $context
     * @param RegionInterfaceFactory $regionFactory
     * @param RegionRepositoryInterface $regionRepository
     * @param HydratorInterface $hydrator
     * @param Registry $registry
     * @param RegionCityRelationProcessor $regionCityRelationProcessor
     */
    public function __construct(
        Context $context,
        RegionInterfaceFactory $regionFactory,
        RegionRepositoryInterface $regionRepository,
        HydratorInterface $hydrator,
        Registry $registry,
        RegionCityRelationProcessor $regionCityRelationProcessor
    ) {
        parent::__construct($context);
        $this->regionFactory = $regionFactory;
        $this->regionRepository = $regionRepository;
        $this->hydrator = $hydrator;
        $this->registry = $registry;
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
                if (isset($regionRequestData['_use_default'])) {
                    // UI component sends value even if field is disabled, so 'Use Default' values must be set to null
                    foreach ($regionRequestData['_use_default'] as $field => $useDefaultState) {
                        if (1 === (int)$useDefaultState) {
                            $requestData[$field] = null;
                        }
                    }
                }
                $regionId = !empty($regionRequestData[RegionInterface::REGION_ID])
                    ? $regionRequestData[RegionInterface::REGION_ID] : null;

                if ($regionId) {
                    $region = $this->regionRepository->get($regionId);
                } else {
                    /** @var RegionInterface $region */
                    $region = $this->regionFactory->create();
                }
                $region = $this->hydrator->hydrate($region, $regionRequestData);
                $regionId = $this->regionRepository->save($region);

                $citiesRequestData = $this->getRequest()->getParam('cities', []);
                if ($citiesRequestData) {
                    $this->regionCityRelationProcessor->process($regionId, $citiesRequestData['assigned_cities']);
                }

                // Keep data for plugins on Save controller. Now we can not call to separate services from one form.
                $this->registry->register(self::REGISTRY_REGION_ID_KEY, $regionId);

                $this->messageManager->addSuccessMessage(__('The Region has been saved.'));
                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath('*/*/edit', [
                        RegionInterface::REGION_ID => $regionId,
                        '_current' => true,
                    ]);
                } elseif ($this->getRequest()->getParam('redirect_to_new')) {
                    $resultRedirect->setPath('*/*/new', [
                        '_current' => true,
                    ]);
                } else {
                    $resultRedirect->setPath('*/*/');
                }
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('The Region does not exist.'));
                $resultRedirect->setPath('*/*/');
            } catch (ValidatorException $e) {
                foreach ($e->getErrors() as $error) {
                    $this->messageManager->addErrorMessage($error);
                }
                if (!empty($regionId)) {
                    $resultRedirect->setPath('*/*/edit', [
                        RegionInterface::REGION_ID => $regionId,
                        '_current' => true,
                    ]);
                } else {
                    $resultRedirect->setPath('*/*/');
                }
            } catch (CouldNotSaveException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                if (!empty($regionId)) {
                    $resultRedirect->setPath('*/*/edit', [
                        RegionInterface::REGION_ID => $regionId,
                        '_current' => true,
                    ]);
                } else {
                    $resultRedirect->setPath('*/*/');
                }
            }
        } else {
            $this->messageManager->addErrorMessage(__('Wrong request.'));
            $resultRedirect->setPath('*/*');
        }
        return $resultRedirect;
    }
}
