<?php
namespace Engine\JsTree\Block\Adminhtml;

use Engine\JsTree\Ui\DataProvider\JsTreeDataInterface;
use Engine\JsTree\Ui\DataProvider\JsTreeDataProviderInterface;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Json\Helper\Data;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @api
 */
class Tree extends Template
{
    /**
     * @var string
     */
    protected $_template = 'tree.phtml';

    /**
     * @var JsTreeDataProviderInterface
     */
    private $jsTreeDataProvider;

    /**
     * @var Data
     */
    private $jsonHelper;

    /**
     * @var JsTreeDataInterface|null
     */
    private $jsTreeData;

    /**
     * @param Context $context
     * @param JsTreeDataProviderInterface $jsTreeDataProvider
     * @param Data $jsonHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        JsTreeDataProviderInterface $jsTreeDataProvider,
        Data $jsonHelper,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->jsTreeDataProvider = $jsTreeDataProvider;
        $this->jsonHelper = $jsonHelper;
    }

    /**
     * @return bool
     */
    public function isNotEmpty()
    {
        $jsTreeData = $this->getJsTreeData();
        return $jsTreeData->isNotEmpty();
    }

    /**
     * @return string
     */
    public function getTreeId()
    {
        $jsTreeData = $this->getJsTreeData();
        return $jsTreeData->getTreeId();
    }

    /**
     * @return string
     */
    public function getJsComponentConfig()
    {
        $jsTreeData = $this->getJsTreeData();
        $json = $this->jsonHelper->jsonEncode($jsTreeData->getJsComponentConfig());
        return $this->escapeHtml($json);
    }

    /**
     * @return JsTreeDataInterface
     */
    private function getJsTreeData()
    {
        if (null === $this->jsTreeData) {
            $this->jsTreeData = $this->jsTreeDataProvider->provide();
        }
        return $this->jsTreeData;
    }
}
