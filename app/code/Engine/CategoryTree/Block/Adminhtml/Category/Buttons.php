<?php
namespace Engine\CategoryTree\Block\Adminhtml\Category;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Widget\Button\ButtonList;
use Magento\Backend\Block\Widget\Button\ContextInterface;
use Magento\Backend\Block\Widget\Button\Item;
use Magento\Backend\Block\Widget\Button\ToolbarInterface;
use Magento\Backend\Block\Widget\Context;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class Buttons extends Template implements ContextInterface
{
    /**
     * @var ButtonList
     */
    private $buttonList;

    /**
     * @var ToolbarInterface
     */
    private $toolbar;

    /**
     * @param Context $context
     * @param array $data
     */
    public function __construct(Context $context, array $data = [])
    {
        $this->buttonList = $context->getButtonList();
        $this->toolbar = $context->getButtonToolbar();
        parent::__construct($context, $data);
    }

    /**
     * Modify button labels
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->buttonList->add('add_button', [
            'label' => 'Add Category',
            'onclick' => 'setLocation(\'' . $this->getUrl('*/*/new') . '\')',
            'class' => 'primary',
        ]);
    }

    /**
     * Push buttons to toolbar
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->toolbar->pushButtons($this, $this->buttonList);
        parent::_prepareLayout();
        return $this;
    }

    /**
     * Check whether button rendering is allowed in current context
     *
     * @param Item $item
     * @return bool
     */
    public function canRender(Item $item)
    {
        return !$item->isDeleted();
    }
}
