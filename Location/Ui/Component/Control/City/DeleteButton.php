<?php
namespace Engine\Location\Ui\Component\Control\City;

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
        $cityId = $this->context->getRequest()->getParam('city_id');
        if (null !== $cityId) {
            $message = __('Are you sure you want to do this?');
            $url = $this->context->getUrlBuilder()->getUrl('*/*/delete');
            $data = [
                'label' => __('Delete'),
                'class' => 'delete',
                'on_click' => "deleteConfirm('{$message}', '{$url}', {data:{city_id:{$cityId}}})",
                'sort_order' => 20,
            ];
        }
        return $data;
    }
}
