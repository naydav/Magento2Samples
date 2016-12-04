<?php
namespace Engine\PerStoreDataSupport\Ui\Component\Control;

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
     * @var string
     */
    private $idFieldName;

    /**
     * @param Context $context
     * @param string $idFieldName
     */
    public function __construct(
        Context $context,
        $idFieldName
    ) {
        $this->context = $context;
        $this->idFieldName = $idFieldName;
    }

    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        $data = [];
        $id = $this->context->getRequest()->getParam($this->idFieldName);
        if (null !== $id) {
            $message = __('Are you sure you want to do this?');
            $url = $this->context->getUrlBuilder()->getUrl('*/*/delete');
            $data = [
                'label' => __('Delete'),
                'class' => 'delete',
                'on_click' => "deleteConfirm('{$message}', '{$url}', {data:{{$this->idFieldName}:{$id}}})",
                'sort_order' => 20,
            ];
        }
        return $data;
    }
}
