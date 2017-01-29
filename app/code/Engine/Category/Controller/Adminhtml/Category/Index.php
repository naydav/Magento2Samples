<?php
namespace Engine\Category\Controller\Adminhtml\Category;

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
    const ADMIN_RESOURCE = 'Engine_Category::category_category';

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Engine_Category::category_category')
            ->addBreadcrumb(__('Categories'), __('List'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Categories'));
        return $resultPage;
    }
}