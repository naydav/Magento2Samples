<?php
namespace Engine\CharacteristicGroup\Controller\Adminhtml\Characteristic\SavePlugin;

use Engine\Characteristic\Controller\Adminhtml\Characteristic\Save;
use Engine\CharacteristicGroup\Model\CharacteristicGroupCharacteristicRelation\CharacteristicRelationsProcessor;
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
     * @var CharacteristicRelationsProcessor
     */
    private $characteristicRelationsProcessor;

    /**
     * @param RequestInterface $request
     * @param Registry $registry
     * @param CharacteristicRelationsProcessor $characteristicRelationsProcessor
     */
    public function __construct(
        RequestInterface $request,
        Registry $registry,
        CharacteristicRelationsProcessor $characteristicRelationsProcessor
    ) {
        $this->request = $request;
        $this->registry = $registry;
        $this->characteristicRelationsProcessor = $characteristicRelationsProcessor;
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
        $characteristicId = $this->registry->registry(Save::REGISTRY_CHARACTERISTIC_ID_KEY);
        $relationsRequestData = $this->request->getParam('characteristic_groups', []);
        if ($characteristicId && $relationsRequestData) {
            try {
                $this->characteristicRelationsProcessor->process(
                    $characteristicId,
                    $relationsRequestData['assigned_characteristic_groups']
                );
            } catch (\Exception $e) {
                throw new LocalizedException(__('Cannot assign Characteristics to Characteristic Group'), $e);
            }
        }
        return $result;
    }
}
