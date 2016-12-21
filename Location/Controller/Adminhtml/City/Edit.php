<?php
namespace Engine\Location\Controller\Adminhtml\City;

use Engine\Location\Api\CityRepositoryInterface;
use Engine\Location\Api\Data\CityInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class Edit extends Action
{
    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Engine_Location::city';

    /**
     * @var CityRepositoryInterface
     */
    private $cityRepository;

    /**
     * @param Context $context
     * @param CityRepositoryInterface $cityRepository
     */
    public function __construct(
        Context $context,
        CityRepositoryInterface $cityRepository
    ) {
        parent::__construct($context);
        $this->cityRepository = $cityRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $cityId = $this->getRequest()->getParam(CityInterface::CITY_ID);
        try {
            $city = $this->cityRepository->get($cityId);

            /** @var Page $result */
            $result = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
            $result->setActiveMenu('Engine_Location::city')->addBreadcrumb(__('Edit City'), __('Edit City'));
            $result->getConfig()->getTitle()->prepend(__('Edit City: %1', $city->getTitle()));
        } catch (NoSuchEntityException $e) {
            /** @var Redirect $result */
            $result = $this->resultRedirectFactory->create();
            $this->messageManager->addErrorMessage(__('City with id "%1" does not exist.', $cityId));
            $result->setPath('*/*/index');
        }
        return $result;
    }
}
