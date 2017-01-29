<?php
namespace Engine\CategoryTree\Controller\Adminhtml\Category;

use Engine\CategoryTree\Api\CategoryTreeMovementInterface;
use Engine\Tree\Api\Data\MoveDataInterface;
use Engine\Tree\Api\MoveDataBuilderInterface;
use Engine\Tree\Model\CouldNotMoveException;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class Move extends Action
{
    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Engine_Category::category_category';

    /**
     * @var MoveDataBuilderInterface
     */
    private $moveDataBuilder;

    /**
     * @var CategoryTreeMovementInterface
     */
    private $categoryTreeMovement;

    /**
     * @param Context $context
     * @param MoveDataBuilderInterface $moveDataBuilder
     * @param CategoryTreeMovementInterface $categoryTreeMovement
     */
    public function __construct(
        Context $context,
        MoveDataBuilderInterface $moveDataBuilder,
        CategoryTreeMovementInterface $categoryTreeMovement
    ) {
        parent::__construct($context);
        $this->moveDataBuilder = $moveDataBuilder;
        $this->categoryTreeMovement = $categoryTreeMovement;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $errorMessages = [];
        $request = $this->getRequest();
        $requestData = $request->getParam('moveData', []);

        if ($request->isXmlHttpRequest()
            && $this->getRequest()->isPost()
            && !empty($requestData[MoveDataInterface::ID])
            && !empty($requestData[MoveDataInterface::PARENT_ID])
        ) {
            try {
                $moveData = $this->moveDataBuilder->setId($requestData[MoveDataInterface::ID])
                    ->setParentId($requestData[MoveDataInterface::PARENT_ID])
                    ->setAfterId(
                        isset($requestData[MoveDataInterface::AFTER_ID])
                            ? $requestData[MoveDataInterface::AFTER_ID] : null
                    )
                    ->create();
                $this->categoryTreeMovement->move($moveData);
            } catch (CouldNotMoveException $e) {
                $errorMessages[] = __('[ID: %1] ', $requestData[MoveDataInterface::ID]) . $e->getMessage();
            }
        } else {
            $errorMessages[] = __('Please correct the data sent.');
        }

        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData([
            'messages' => $errorMessages,
            'error' => count($errorMessages),
        ]);
        return $resultJson;
    }
}
