<?php
namespace Engine\CategoryCharacteristicGroup\Controller\Adminhtml\CharacteristicGroup\SavePlugin;

use Engine\CharacteristicGroup\Controller\Adminhtml\CharacteristicGroup\Save;
use Engine\CategoryCharacteristicGroup\Model\CharacteristicGroupRelationsProcessor;
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
     * @var CharacteristicGroupRelationsProcessor
     */
    private $characteristicGroupRelationsProcessor;

    /**
     * @param RequestInterface $request
     * @param Registry $registry
     * @param CharacteristicGroupRelationsProcessor $characteristicGroupRelationsProcessor
     */
    public function __construct(
        RequestInterface $request,
        Registry $registry,
        CharacteristicGroupRelationsProcessor $characteristicGroupRelationsProcessor
    ) {
        $this->request = $request;
        $this->registry = $registry;
        $this->characteristicGroupRelationsProcessor = $characteristicGroupRelationsProcessor;
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
        $characteristicGroupId = $this->registry->registry(Save::REGISTRY_CHARACTERISTIC_GROUP_ID_KEY);
        $relationsRequestData = $this->request->getParam('categories', []);
        if ($characteristicGroupId && $relationsRequestData) {
            try {
                $this->characteristicGroupRelationsProcessor->process(
                    $characteristicGroupId,
                    $relationsRequestData['assigned_categories']
                );
            } catch (\Exception $e) {
                throw new LocalizedException(__('Cannot assign Characteristic Groups to Category'), $e);
            }
        }
        return $result;
    }
}
