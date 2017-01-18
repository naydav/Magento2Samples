<?php
namespace Engine\Category\Controller\Adminhtml\Category;

use Engine\Category\Api\Data\CategoryInterface;
use Engine\Category\Api\Data\CategoryInterfaceFactory;
use Engine\Category\Api\CategoryRepositoryInterface;
use Engine\Category\Model\Category\CategoryBaseValidatorInterface;
use Engine\Framework\Exception\ValidatorException;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\EntityManager\HydratorInterface;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class Validate extends Action
{
    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Engine_Category::category_category';

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
     * @var CategoryBaseValidatorInterface
     */
    private $categoryBaseValidator;

    /**
     * @param Context $context
     * @param CategoryInterfaceFactory $categoryFactory
     * @param CategoryRepositoryInterface $categoryRepository
     * @param HydratorInterface $hydrator
     * @param CategoryBaseValidatorInterface $categoryBaseValidator
     */
    public function __construct(
        Context $context,
        CategoryInterfaceFactory $categoryFactory,
        CategoryRepositoryInterface $categoryRepository,
        HydratorInterface $hydrator,
        CategoryBaseValidatorInterface $categoryBaseValidator
    ) {
        parent::__construct($context);
        $this->categoryFactory = $categoryFactory;
        $this->categoryRepository = $categoryRepository;
        $this->hydrator = $hydrator;
        $this->categoryBaseValidator = $categoryBaseValidator;
    }

    /**
     * @return Json
     */
    public function execute()
    {
        $errorMessages = [];
        $request = $this->getRequest();
        $requestData = $this->getRequest()->getParam('general');

        if ($request->isXmlHttpRequest() && $this->getRequest()->isPost() && $requestData) {
            $categoryId = !empty($requestData[CategoryInterface::CATEGORY_ID])
                ? $requestData[CategoryInterface::CATEGORY_ID] : null;

            if ($categoryId) {
                $category = $this->categoryRepository->get($categoryId);
            } else {
                /** @var CategoryInterface $category */
                $category = $this->categoryFactory->create();
            }
            $category = $this->hydrator->hydrate($category, $requestData);

            try {
                $this->categoryBaseValidator->validate($category);
            } catch (ValidatorException $e) {
                $errorMessages = $e->getErrors();
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
