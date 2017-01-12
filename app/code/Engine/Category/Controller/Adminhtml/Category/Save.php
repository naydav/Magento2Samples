<?php
namespace Engine\Category\Controller\Adminhtml\Category;

use Engine\Category\Api\Data\CategoryInterface;
use Engine\Category\Api\Data\CategoryInterfaceFactory;
use Engine\Category\Api\CategoryRepositoryInterface;
use Engine\Framework\Exception\ValidatorException;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class Save extends Action
{
    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Engine_Category::category_category';

    /**
     * Registry category_id key
     */
    const REGISTRY_CATEGORY_ID_KEY = 'category_id';

    /**
     * @var CategoryInterfaceFactory
     */
    private $categoryFactory;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @param Context $context
     * @param CategoryInterfaceFactory $categoryFactory
     * @param CategoryRepositoryInterface $categoryRepository
     * @param HydratorInterface $hydrator
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        CategoryInterfaceFactory $categoryFactory,
        CategoryRepositoryInterface $categoryRepository,
        HydratorInterface $hydrator,
        Registry $registry
    ) {
        parent::__construct($context);
        $this->categoryFactory = $categoryFactory;
        $this->categoryRepository = $categoryRepository;
        $this->hydrator = $hydrator;
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $requestData = $this->getRequest()->getParam('general');
        if ($this->getRequest()->isPost() && $requestData) {
            try {
                $useDefaults = $this->getRequest()->getParam('use_default', []);
                if ($useDefaults) {
                    foreach ($useDefaults as $field => $useDefaultState) {
                        if (1 === (int)$useDefaultState) {
                            $requestData[$field] = null;
                        }
                    }
                }
                $categoryId = !empty($requestData[CategoryInterface::CATEGORY_ID])
                    ? $requestData[CategoryInterface::CATEGORY_ID] : null;

                if ($categoryId) {
                    $category = $this->categoryRepository->get($categoryId);
                } else {
                    /** @var CategoryInterface $category */
                    $category = $this->categoryFactory->create();
                }
                $category = $this->hydrator->hydrate($category, $requestData);
                $categoryId = $this->categoryRepository->save($category);
                // Keep data for plugins on Save controller. Now we can not call separate services from one form.
                $this->registry->register(self::REGISTRY_CATEGORY_ID_KEY, $categoryId);

                $this->messageManager->addSuccessMessage(__('The Category has been saved.'));
                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath('*/*/edit', [
                        CategoryInterface::CATEGORY_ID => $categoryId,
                        '_current' => true,
                    ]);
                } elseif ($this->getRequest()->getParam('redirect_to_new')) {
                    $resultRedirect->setPath('*/*/new', [
                        '_current' => true,
                    ]);
                } else {
                    $resultRedirect->setPath('*/*/');
                }
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('The Category does not exist.'));
                $resultRedirect->setPath('*/*/');
            } catch (ValidatorException $e) {
                foreach ($e->getErrors() as $error) {
                    $this->messageManager->addErrorMessage($error);
                }
                if (!empty($categoryId)) {
                    $resultRedirect->setPath('*/*/edit', [
                        CategoryInterface::CATEGORY_ID => $categoryId,
                        '_current' => true,
                    ]);
                } else {
                    $resultRedirect->setPath('*/*/');
                }
            } catch (CouldNotSaveException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                if (!empty($categoryId)) {
                    $resultRedirect->setPath('*/*/edit', [
                        CategoryInterface::CATEGORY_ID => $categoryId,
                        '_current' => true,
                    ]);
                } else {
                    $resultRedirect->setPath('*/*/');
                }
            }
        } else {
            $this->messageManager->addErrorMessage(__('Wrong request.'));
            $resultRedirect->setPath('*/*');
        }
        return $resultRedirect;
    }
}
