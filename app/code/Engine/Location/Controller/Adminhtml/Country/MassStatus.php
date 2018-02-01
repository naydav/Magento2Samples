<?php
declare(strict_types=1);

namespace Engine\Location\Controller\Adminhtml\Country;

use Engine\Location\Api\Data\CountryInterface;
use Engine\Location\Api\CountryRepositoryInterface;
use Engine\Magento\Ui\Component\MassAction\Filter;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Validation\ValidationException;

/**
 * @author naydav <valeriy.nayda@gmail.com>
 */
class MassStatus extends Action
{
    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Engine_Location::location_country';

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

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
     * @param DataObjectHelper $dataObjectHelper
     * @param CountryRepositoryInterface $countryRepository
     * @param Filter $massActionFilter
     */
    public function __construct(
        Context $context,
        DataObjectHelper $dataObjectHelper,
        CountryRepositoryInterface $countryRepository,
        Filter $massActionFilter
    ) {
        parent::__construct($context);
        $this->dataObjectHelper = $dataObjectHelper;
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

        $enabled = (int)$this->getRequest()->getParam(CountryInterface::ENABLED);

        $updatedItemsCount = 0;
        foreach ($this->massActionFilter->getIds() as $countryId) {
            try {
                $countryId = (int)$countryId;
                $country = $this->countryRepository->get($countryId);
                $country->setEnabled($enabled);
                $this->countryRepository->save($country);
                $updatedItemsCount++;
            } catch (NoSuchEntityException $e) {
                $errorMessages[] = __(
                    '[ID: %id] The Country does not exist.',
                    ['id' => $countryId]
                );
            } catch (ValidationException $e) {
                foreach ($e->getErrors() as $localizedError) {
                    $errorMessages[] = __('[ID: %id] %message', [
                        'id' => $countryId,
                        'message' => $localizedError->getMessage(),
                    ]);
                }
            } catch (CouldNotSaveException $e) {
                $errorMessage = __('[ID: %id] %message', ['id' => $countryId, 'message' => $e->getMessage()]);
                $this->messageManager->addErrorMessage($errorMessage);
            }
        }

        $this->messageManager->addSuccessMessage(__('You updated %count Country(s).', [
            'count' => $updatedItemsCount,
        ]));
        return $this->resultRedirectFactory->create()->setPath('*/*');
    }
}
