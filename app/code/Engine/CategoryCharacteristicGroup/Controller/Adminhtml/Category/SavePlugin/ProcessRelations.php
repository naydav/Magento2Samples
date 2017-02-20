<?php
namespace Engine\CategoryCharacteristicGroup\Controller\Adminhtml\Category\SavePlugin;

use Engine\Category\Controller\Adminhtml\Category\Save;
use Engine\CategoryCharacteristicGroup\Model\CategoryRelationsProcessor;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class ProcessRelations
{
    /**
     * @var RequestInterface $request
     */
    private $request;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var CategoryRelationsProcessor
     */
    private $categoryRelationsProcessor;

    /**
     * @param RequestInterface $request
     * @param Registry $registry
     * @param CategoryRelationsProcessor $categoryRelationsProcessor
     */
    public function __construct(
        RequestInterface $request,
        Registry $registry,
        CategoryRelationsProcessor $categoryRelationsProcessor
    ) {
        $this->request = $request;
        $this->registry = $registry;
        $this->categoryRelationsProcessor = $categoryRelationsProcessor;
    }

    /**
     * @param Save $subject
     * @param ResultInterface $result
     * @return ResultInterface
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterExecute(Save $subject, ResultInterface $result)
    {
        $categoryId = $this->registry->registry(Save::REGISTRY_CATEGORY_ID_KEY);
        $relationsRequestData = $this->request->getParam('characteristic_groups', []);
        if ($categoryId && $relationsRequestData) {
            try {
                $this->categoryRelationsProcessor->process(
                    $categoryId,
                    $relationsRequestData['assigned_characteristic_groups']
                );
            } catch (\Exception $e) {
                throw new LocalizedException(__('Cannot assign Characteristic Groups to Category'), $e);
            }
        }
        return $result;
    }
}
