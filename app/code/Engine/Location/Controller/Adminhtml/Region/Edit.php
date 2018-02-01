<?php
declare(strict_types=1);

namespace Engine\Location\Controller\Adminhtml\Region;

use Engine\Location\Api\RegionRepositoryInterface;
use Engine\Location\Api\Data\RegionInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @author naydav <valeriy.nayda@gmail.com>
 */
class Edit extends Action
{
    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Engine_Location::location_region';

    /**
     * @var RegionRepositoryInterface
     */
    private $regionRepository;

    /**
     * @param Context $context
     * @param RegionRepositoryInterface $regionRepository
     */
    public function __construct(
        Context $context,
        RegionRepositoryInterface $regionRepository
    ) {
        parent::__construct($context);
        $this->regionRepository = $regionRepository;
    }

    /**
     * @inheritdoc
     */
    public function execute(): ResultInterface
    {
        $regionId = $this->getRequest()->getParam(RegionInterface::REGION_ID);
        try {
            $region = $this->regionRepository->get((int)$regionId);

            /** @var Page $result */
            $result = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
            $result->setActiveMenu('Engine_Location::location_region')
                ->addBreadcrumb(__('Edit Region'), __('Edit Region'));
            $result->getConfig()
                ->getTitle()
                ->prepend(
                    __('Edit Region: %name', ['name' => $region->getName()])
                );
        } catch (NoSuchEntityException $e) {
            /** @var Redirect $result */
            $result = $this->resultRedirectFactory->create();
            $this->messageManager->addErrorMessage(
                __('Region with id "%id" does not exist.', ['id' => $regionId])
            );
            $result->setPath('*/*');
        }
        return $result;
    }
}
