<?php
namespace Engine\CharacteristicGroup\Controller\Adminhtml\CharacteristicGroup;

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
    const ADMIN_RESOURCE = 'Engine_CharacteristicGroup::characteristic_group_characteristic_group';

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Engine_CharacteristicGroup::characteristic_group_characteristic_group')
            ->addBreadcrumb(__('Characteristic Groups'), __('List'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Characteristic Groups'));
        return $resultPage;
    }
}
