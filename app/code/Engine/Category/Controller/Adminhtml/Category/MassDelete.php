<?php
namespace Engine\Category\Controller\Adminhtml\Category;

use Engine\Category\Api\Data\CategoryInterface;
use Engine\Category\Api\CategoryRepositoryInterface;
use Engine\MagentoFix\Ui\Component\MassAction\Filter;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\CouldNotDeleteException;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class MassDelete extends Action
{
    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Engine_Category::category_category';

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var Filter
     */
    private $massActionFilter;

    /**
     * @param Context $context
     * @param CategoryRepositoryInterface $categoryRepository
     * @param Filter $massActionFilter
     */
    public function __construct(
        Context $context,
        CategoryRepositoryInterface $categoryRepository,
        Filter $massActionFilter
    ) {
        parent::__construct($context);
        $this->categoryRepository = $categoryRepository;
        $this->massActionFilter = $massActionFilter;
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        if ($this->getRequest()->isPost()) {
            $deletedItemsCount = 0;
            foreach ($this->massActionFilter->getIds() as $id) {
                try {
                    $this->categoryRepository->deleteById($id);
                    $deletedItemsCount++;
                } catch (CouldNotDeleteException $e) {
                    $errorMessage = __('[ID: %1] ', $id)
                        . $e->getMessage();
                    $this->messageManager->addErrorMessage($errorMessage);
                }
            }
            $this->messageManager->addSuccessMessage(__('You deleted %1 Category(s).', $deletedItemsCount));
        } else {
            $this->messageManager->addErrorMessage(__('Wrong request.'));
        }
        return $this->resultRedirectFactory->create()->setPath('*/*');
    }
}
