<?php
namespace Engine\Backend\Model;

use Engine\Backend\Api\StoreContextInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class StoreContext implements StoreContextInterface
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
     * @var RequestInterface
     */
    private $request;

    /**
     * @var string
     */
    private $paramName;

    /**
     * @var int|null
     */
    private $currentStoreId;

    /**
     * @param StoreManagerInterface $storeManager
     * @param RequestInterface $request
     * @param string $paramName
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        RequestInterface $request,
        $paramName = self::DEFAULT_STORE_ID_PARAM_NAME
    ) {
        $this->storeManager = $storeManager;
        $this->request = $request;
        $this->paramName = $paramName;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentStore()
    {
        if (null === $this->currentStoreId) {
            $this->currentStoreId = $this->request->getParam($this->paramName, Store::DEFAULT_STORE_ID);
        }
        $store = $this->storeManager->getStore($this->currentStoreId);
        return $store;
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrentStoreById($storeId)
    {
        $this->currentStoreId = (int)$storeId;
    }
}
