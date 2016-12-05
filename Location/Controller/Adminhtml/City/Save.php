<?php
namespace Engine\Location\Controller\Adminhtml\City;

use Engine\Location\Api\Data\CityInterface;
use Engine\Location\Api\Data\CityInterfaceFactory;
use Engine\Location\Api\CityRepositoryInterface;
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
    const ADMIN_RESOURCE = 'Engine_Location::city';

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
     * @param Context $context
     * @param CityInterfaceFactory $cityFactory
     * @param CityRepositoryInterface $cityRepository
     * @param HydratorInterface $hydrator
     */
    public function __construct(
        Context $context,
        CityInterfaceFactory $cityFactory,
        CityRepositoryInterface $cityRepository,
        HydratorInterface $hydrator
    ) {
        parent::__construct($context);
        $this->cityFactory = $cityFactory;
        $this->cityRepository = $cityRepository;
        $this->hydrator = $hydrator;
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
                $cityId = !empty($requestData['city_id']) ? $requestData['city_id'] : null;

                if ($cityId) {
                    $city = $this->cityRepository->get($cityId);
                } else {
                    /** @var CityInterface $city */
                    $city = $this->cityFactory->create();
                }
                $this->hydrator->hydrate($city, $requestData);
                $this->cityRepository->save($city);

                $this->messageManager->addSuccessMessage(__('The City has been saved.'));
                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath('*/*/edit', ['city_id' => $city->getCityId(), '_current' => true]);
                } else {
                    $resultRedirect->setPath('*/*/');
                }
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('The City does not exist.'));
                $resultRedirect->setPath('*/*/');
            } catch (CouldNotSaveException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                if ($cityId) {
                    $resultRedirect->setPath('*/*/edit', ['city_id' => $cityId, '_current' => true]);
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
