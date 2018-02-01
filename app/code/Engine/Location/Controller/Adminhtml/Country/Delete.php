<?php
declare(strict_types=1);

namespace Engine\Location\Controller\Adminhtml\Country;

use Engine\Location\Api\CountryRepositoryInterface;
use Engine\Location\Api\Data\CountryInterface;
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
    const ADMIN_RESOURCE = 'Engine_Location::location_country';

    /**
     * @var CountryRepositoryInterface
     */
    private $countryRepository;

    /**
     * @param Context $context
     * @param CountryRepositoryInterface $countryRepository
     */
    public function __construct(
        Context $context,
        CountryRepositoryInterface $countryRepository
    ) {
        parent::__construct($context);
        $this->countryRepository = $countryRepository;
    }

    /**
     * @inheritdoc
     */
    public function execute(): ResultInterface
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        $countryId = $this->getRequest()->getPost(CountryInterface::COUNTRY_ID);
        if (null === $countryId) {
            $this->messageManager->addErrorMessage(__('Wrong request.'));
            return $resultRedirect->setPath('*/*');
        }

        try {
            $countryId = (int)$countryId;
            $this->countryRepository->deleteById($countryId);
            $this->messageManager->addSuccessMessage(__('The Country has been deleted.'));
            $resultRedirect->setPath('*/*');
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(
                __('Country with id "%id" does not exist.', ['id' => $countryId])
            );
            $resultRedirect->setPath('*/*');
        } catch (CouldNotDeleteException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $resultRedirect->setPath('*/*/edit', [
            CountryInterface::COUNTRY_ID => $countryId,
                '_current' => true,
            ]);
        }
        return $resultRedirect;
    }
}
