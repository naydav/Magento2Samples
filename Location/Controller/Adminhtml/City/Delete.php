<?php
namespace Engine\Location\Controller\Adminhtml\City;

use Engine\Location\Api\CityRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class Delete extends Action
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
        $resultRedirect = $this->resultRedirectFactory->create();

        $cityId = $this->getRequest()->getPost('city_id');
        if ($this->getRequest()->isPost() && null !== $cityId) {
            try {
                $this->cityRepository->deleteById($cityId);
                $this->messageManager->addSuccessMessage(__('The city has been deleted.'));
                $resultRedirect->setPath('*/*/index');
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('City with id "%1" does not exist.', $cityId));
                $resultRedirect->setPath('*/*/index');
            } catch (CouldNotDeleteException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $resultRedirect->setPath('*/*/edit', ['city_id' => $cityId, '_current' => true]);
            }
        } else {
            $this->messageManager->addErrorMessage(__('Wrong request.'));
            $resultRedirect->setPath('*/*/index');
        }
        return $resultRedirect;
    }
}
