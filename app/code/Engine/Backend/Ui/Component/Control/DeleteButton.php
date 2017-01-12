<?php
namespace Engine\Backend\Ui\Component\Control;

use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @api
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
     * @var string
     */
    private $routePath;

    /**
     * @param Context $context
     * @param string $idFieldName
     * @param string $routePath
     */
    public function __construct(
        Context $context,
        $idFieldName,
        $routePath = '*/*/delete'
    ) {
        $this->context = $context;
        $this->idFieldName = $idFieldName;
        $this->routePath = $routePath;
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
            $url = $this->context->getUrlBuilder()->getUrl($this->routePath);
            $data = [
                'label' => __('Delete'),
                'class' => 'delete',
                'on_click' => "deleteConfirm('{$message}', '{$url}', {data:{{$this->idFieldName}:{$id}}})",
                'sort_order' => 30,
            ];
        }
        return $data;
    }
}
