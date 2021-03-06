<?php
declare(strict_types=1);

namespace Engine\Location\Controller\Adminhtml\City;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

/**
 * @author naydav <valeriy.nayda@gmail.com>
 */
class NewAction extends Action
{
    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Engine_Location::location_city';

    /**
     * @inheritdoc
     */
    public function execute(): ResultInterface
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Engine_Location::location_city');
        $resultPage->getConfig()->getTitle()->prepend(__('New City'));
        return $resultPage;
    }
}
