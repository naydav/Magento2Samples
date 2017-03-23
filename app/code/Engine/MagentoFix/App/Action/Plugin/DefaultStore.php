<?php
namespace Engine\MagentoFix\App\Action\Plugin;

use Magento\Framework\App\Action\AbstractAction;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

/**
 * MAGETWO-62413: Using default storeview as current in DefaultStore plugin is applied on FrontControllerInterface
 *
 * @author naydav <valeriy.nayda@gmail.com>
 */
class DefaultStore
{
    /**
     * Store id param name
     */
    const DEFAULT_STORE_ID_PARAM_NAME = 'store';

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var string
     */
    private $paramName;

    /**
     * @param StoreManagerInterface $storeManager
     * @param $paramName
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        $paramName = self::DEFAULT_STORE_ID_PARAM_NAME
    ) {
        $this->storeManager = $storeManager;
        $this->paramName = $paramName;
    }

    /**
     * @param AbstractAction $subject
     * @param RequestInterface $request
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeDispatch(AbstractAction $subject, RequestInterface $request)
    {
        $currentStoreId = $request->getParam($this->paramName, Store::DEFAULT_STORE_ID);
        $this->storeManager->setCurrentStore($currentStoreId);
    }
}
