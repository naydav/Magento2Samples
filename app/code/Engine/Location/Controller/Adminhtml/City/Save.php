<?php
namespace Engine\Location\Controller\Adminhtml\City;

use Engine\Location\Api\Data\CityInterface;
use Engine\Location\Api\Data\CityInterfaceFactory;
use Engine\Location\Api\CityRepositoryInterface;
use Engine\Validation\Exception\ValidatorException;
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
    const ADMIN_RESOURCE = 'Engine_Location::location_city';

    /**
     * Registry city_id key
     */
    const REGISTRY_CITY_ID_KEY = 'city_id';

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
     * @var Registry
     */
    private $registry;

    /**
     * @param Context $context
     * @param CityInterfaceFactory $cityFactory
     * @param CityRepositoryInterface $cityRepository
     * @param HydratorInterface $hydrator
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        CityInterfaceFactory $cityFactory,
        CityRepositoryInterface $cityRepository,
        HydratorInterface $hydrator,
        Registry $registry
    ) {
        parent::__construct($context);
        $this->cityFactory = $cityFactory;
        $this->cityRepository = $cityRepository;
        $this->hydrator = $hydrator;
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $requestData = $this->getRequest()->getParam('general');
        if ($this->getRequest()->isPost() && $requestData) {
            try {
                $useDefaults = $this->getRequest()->getParam('use_default', []);
                if ($useDefaults) {
                    foreach ($useDefaults as $field => $useDefaultState) {
                        if (1 === (int)$useDefaultState) {
                            $requestData[$field] = null;
                        }
                    }
                }
                $cityId = !empty($requestData[CityInterface::CITY_ID])
                    ? $requestData[CityInterface::CITY_ID] : null;

                if ($cityId) {
                    $city = $this->cityRepository->get($cityId);
                } else {
                    /** @var CityInterface $city */
                    $city = $this->cityFactory->create();
                }
                $city = $this->hydrator->hydrate($city, $requestData);
                $cityId = $this->cityRepository->save($city);
                // Keep data for plugins on Save controller. Now we can not call separate services from one form.
                $this->registry->register(self::REGISTRY_CITY_ID_KEY, $cityId);

                $this->messageManager->addSuccessMessage(__('The City has been saved.'));
                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath('*/*/edit', [
                        CityInterface::CITY_ID => $cityId,
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
                $this->messageManager->addErrorMessage(__('The City does not exist.'));
                $resultRedirect->setPath('*/*/');
            } catch (ValidatorException $e) {
                foreach ($e->getErrors() as $error) {
                    $this->messageManager->addErrorMessage($error);
                }
                if (!empty($cityId)) {
                    $resultRedirect->setPath('*/*/edit', [
                        CityInterface::CITY_ID => $cityId,
                        '_current' => true,
                    ]);
                } else {
                    $resultRedirect->setPath('*/*/');
                }
            } catch (CouldNotSaveException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                if (!empty($cityId)) {
                    $resultRedirect->setPath('*/*/edit', [
                        CityInterface::CITY_ID => $cityId,
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
