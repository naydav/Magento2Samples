<?php
declare(strict_types=1);

namespace Engine\Location\Controller\Adminhtml\Country;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

/**
 * @author naydav <valeriy.nayda@gmail.com>
 */
class Index extends Action
{
    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Engine_Location::location_country';

    /**
     * @inheritdoc
     */
    public function execute(): ResultInterface
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Engine_Location::location_country')
            ->addBreadcrumb(__('Countries'), __('List'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Countries'));
        return $resultPage;
    }
}
