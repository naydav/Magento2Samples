<?php
declare(strict_types=1);

namespace Engine\Location\Controller\Adminhtml\Country;

use Engine\Location\Api\CountryRepositoryInterface;
use Engine\Magento\Ui\Component\MassAction\Filter;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @author naydav <valeriy.nayda@gmail.com>
 */
class MassDelete extends Action
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
     * @var Filter
     */
    private $massActionFilter;

    /**
     * @param Context $context
     * @param CountryRepositoryInterface $countryRepository
     * @param Filter $massActionFilter
     */
    public function __construct(
        Context $context,
        CountryRepositoryInterface $countryRepository,
        Filter $massActionFilter
    ) {
        parent::__construct($context);
        $this->countryRepository = $countryRepository;
        $this->massActionFilter = $massActionFilter;
    }

    /**
     * @inheritdoc
     */
    public function execute(): ResultInterface
    {
        if (false === $this->getRequest()->isPost()) {
            $this->messageManager->addErrorMessage(__('Wrong request.'));
            return $this->resultRedirectFactory->create()->setPath('*/*');
        }

        $deletedItemsCount = 0;
        foreach ($this->massActionFilter->getIds() as $countryId) {
            try {
                $countryId = (int)$countryId;
                $this->countryRepository->deleteById($countryId);
                $deletedItemsCount++;
            } catch (NoSuchEntityException $e) {
                $errorMessage = __('[ID: %id] %message', ['id' => $countryId, 'message' => $e->getMessage()]);
                $this->messageManager->addErrorMessage($errorMessage);
            } catch (CouldNotDeleteException $e) {
                $errorMessage = __('[ID: %id] %message', ['id' => $countryId, 'message' => $e->getMessage()]);
                $this->messageManager->addErrorMessage($errorMessage);
            }
        }

        $this->messageManager->addSuccessMessage(__('You deleted %count Country(s).', ['count' => $deletedItemsCount]));
        return $this->resultRedirectFactory->create()->setPath('*/*');
    }
}
