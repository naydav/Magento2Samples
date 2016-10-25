<?php
namespace Engine\Location\Controller\Adminhtml\Region;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Controller\ResultFactory;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class Index extends Action
{
    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Engine_Location::region';

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Engine_Location::region')
            ->addBreadcrumb(__('Region'), __('List'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Regions'));
        return $resultPage;
    }
}