<?php
declare(strict_types=1);

namespace Engine\Location\Controller\Adminhtml\City;

use Engine\Location\Api\CityRepositoryInterface;
use Engine\Location\Api\Data\CityInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @author naydav <valeriy.nayda@gmail.com>
 */
class Delete extends Action
{
    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Engine_Location::location_city';

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
     * @inheritdoc
     */
    public function execute(): ResultInterface
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        $cityId = $this->getRequest()->getPost(CityInterface::CITY_ID);
        if (null === $cityId) {
            $this->messageManager->addErrorMessage(__('Wrong request.'));
            return $resultRedirect->setPath('*/*');
        }

        try {
            $cityId = (int)$cityId;
            $this->cityRepository->deleteById($cityId);
            $this->messageManager->addSuccessMessage(__('The City has been deleted.'));
            $resultRedirect->setPath('*/*');
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(
                __('City with id "%id" does not exist.', ['id' => $cityId])
            );
            $resultRedirect->setPath('*/*');
        } catch (CouldNotDeleteException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $resultRedirect->setPath('*/*/edit', [
            CityInterface::CITY_ID => $cityId,
                '_current' => true,
            ]);
        }
        return $resultRedirect;
    }
}
