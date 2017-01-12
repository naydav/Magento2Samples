<?php
namespace Engine\Category\Controller\Adminhtml\Category;

use Engine\Category\Api\Data\CategoryInterface;
use Engine\Category\Api\CategoryRepositoryInterface;
use Engine\Category\Model\Category\ResourceModel\CategoryCollectionFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Ui\Component\MassAction\Filter;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class MassStatus extends Action
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
     * @var CategoryCollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @param Context $context
     * @param CategoryRepositoryInterface $categoryRepository
     * @param Filter $massActionFilter
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param HydratorInterface $hydrator
     */
    public function __construct(
        Context $context,
        CategoryRepositoryInterface $categoryRepository,
        Filter $massActionFilter,
        CategoryCollectionFactory $categoryCollectionFactory,
        HydratorInterface $hydrator
    ) {
        parent::__construct($context);
        $this->categoryRepository = $categoryRepository;
        $this->massActionFilter = $massActionFilter;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->hydrator = $hydrator;
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        if ($this->getRequest()->isPost()) {
            $isEnabled = (int)$this->getRequest()->getParam('is_enabled');

            $updatedItemsCount = 0;
            $collection = $this->massActionFilter->getCollection($this->categoryCollectionFactory->create());
            foreach ($collection as $category) {
                try {
                    $category = $this->categoryRepository->get(
                        $category->getCategoryId()
                    );
                    $category = $this->hydrator->hydrate($category, [
                        CategoryInterface::IS_ENABLED => $isEnabled,
                    ]);
                    $this->categoryRepository->save($category);
                    $updatedItemsCount++;
                } catch (CouldNotSaveException $e) {
                    $errorMessage = __('[ID: %1] ', $category->getCategoryId())
                        . $e->getMessage();
                    $this->messageManager->addErrorMessage($errorMessage);
                }
            }
            $this->messageManager->addSuccessMessage(__('You updated %1 Category(s).', $updatedItemsCount));
        } else {
            $this->messageManager->addErrorMessage(__('Wrong request.'));
        }
        return $this->resultRedirectFactory->create()->setPath('*/*');
    }
}
