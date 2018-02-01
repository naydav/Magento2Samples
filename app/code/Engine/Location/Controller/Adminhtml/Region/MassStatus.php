<?php
declare(strict_types=1);

namespace Engine\Location\Controller\Adminhtml\Region;

use Engine\Location\Api\Data\RegionInterface;
use Engine\Location\Api\RegionRepositoryInterface;
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
    const ADMIN_RESOURCE = 'Engine_Location::location_region';

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var RegionRepositoryInterface
     */
    private $regionRepository;

    /**
     * @var Filter
     */
    private $massActionFilter;

    /**
     * @param Context $context
     * @param DataObjectHelper $dataObjectHelper
     * @param RegionRepositoryInterface $regionRepository
     * @param Filter $massActionFilter
     */
    public function __construct(
        Context $context,
        DataObjectHelper $dataObjectHelper,
        RegionRepositoryInterface $regionRepository,
        Filter $massActionFilter
    ) {
        parent::__construct($context);
        $this->dataObjectHelper = $dataObjectHelper;
        $this->regionRepository = $regionRepository;
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

        $enabled = (int)$this->getRequest()->getParam(RegionInterface::ENABLED);

        $updatedItemsCount = 0;
        foreach ($this->massActionFilter->getIds() as $regionId) {
            try {
                $regionId = (int)$regionId;
                $region = $this->regionRepository->get($regionId);
                $region->setEnabled($enabled);
                $this->regionRepository->save($region);
                $updatedItemsCount++;
            } catch (NoSuchEntityException $e) {
                $errorMessages[] = __(
                    '[ID: %id] The Region does not exist.',
                    ['id' => $regionId]
                );
            } catch (ValidationException $e) {
                foreach ($e->getErrors() as $localizedError) {
                    $errorMessages[] = __('[ID: %id] %message', [
                        'id' => $regionId,
                        'message' => $localizedError->getMessage(),
                    ]);
                }
            } catch (CouldNotSaveException $e) {
                $errorMessage = __('[ID: %id] %message', ['id' => $regionId, 'message' => $e->getMessage()]);
                $this->messageManager->addErrorMessage($errorMessage);
            }
        }

        $this->messageManager->addSuccessMessage(__('You updated %count Region(s).', [
            'count' => $updatedItemsCount,
        ]));
        return $this->resultRedirectFactory->create()->setPath('*/*');
    }
}
