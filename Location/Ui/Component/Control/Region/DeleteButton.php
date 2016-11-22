<?php
namespace Engine\Location\Ui\Component\Control\Region;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class DeleteButton implements ButtonProviderInterface
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        $data = [];
        $regionId = $this->context->getRequest()->getParam('region_id');
        if (null !== $regionId) {
            $message = __('Are you sure you want to do this?');
            $url = $this->context->getUrlBuilder()->getUrl('*/*/delete');
            $data = [
                'label' => __('Delete'),
                'class' => 'delete',
                'on_click' => "deleteConfirm('{$message}', '{$url}', {data:{region_id:{$regionId}}})",
                'sort_order' => 20,
            ];
        }
        return $data;
    }
}
