<?php
namespace Engine\Category\Controller\Adminhtml\Category;

use Engine\Category\Api\CategoryRepositoryInterface;
use Engine\Category\Api\Data\CategoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class Delete extends Action
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
     * @param Context $context
     * @param CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(
        Context $context,
        CategoryRepositoryInterface $categoryRepository
    ) {
        parent::__construct($context);
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        $categoryId = $this->getRequest()->getPost(CategoryInterface::CATEGORY_ID);
        if ($this->getRequest()->isPost() && null !== $categoryId) {
            try {
                $this->categoryRepository->deleteById($categoryId);
                $this->messageManager->addSuccessMessage(__('The Category has been deleted.'));
                $resultRedirect->setPath('*/*');
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(
                    __('Category with id "%1" does not exist.', $categoryId)
                );
                $resultRedirect->setPath('*/*');
            } catch (CouldNotDeleteException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $resultRedirect->setPath('*/*/edit', [
                    CategoryInterface::CATEGORY_ID => $categoryId,
                    '_current' => true,
                ]);
            }
        } else {
            $this->messageManager->addErrorMessage(__('Wrong request.'));
            $resultRedirect->setPath('*/*');
        }
        return $resultRedirect;
    }
}
