<?php
namespace Engine\Category\Controller\Adminhtml\Category;

use Engine\Category\Api\CategoryRepositoryInterface;
use Engine\Category\Api\Data\CategoryInterface;
use Engine\Validation\Exception\ValidatorException;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class InlineEdit extends Action
{
    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Engine_Category::category_category';

    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @param Context $context
     * @param HydratorInterface $hydrator
     * @param CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(
        Context $context,
        HydratorInterface $hydrator,
        CategoryRepositoryInterface $categoryRepository
    ) {
        parent::__construct($context);
        $this->hydrator = $hydrator;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $errorMessages = [];
        $request = $this->getRequest();
        $requestData = $request->getParam('items', []);

        if ($request->isXmlHttpRequest() && $this->getRequest()->isPost() && $requestData) {
            foreach ($requestData as $itemData) {
                try {
                    $category = $this->categoryRepository->get(
                        $itemData[CategoryInterface::CATEGORY_ID]
                    );
                    $category = $this->hydrator->hydrate($category, $itemData);
                    $this->categoryRepository->save($category);
                } catch (NoSuchEntityException $e) {
                    $errorMessages[] = __(
                        '[ID: %1] The Category does not exist.',
                        $itemData[CategoryInterface::CATEGORY_ID]
                    );
                } catch (ValidatorException $e) {
                    $errorMessages = $e->getErrors();
                } catch (CouldNotSaveException $e) {
                    $errorMessages[] =
                        __('[ID: %1] ', $itemData[CategoryInterface::CATEGORY_ID])
                        . $e->getMessage();
                }
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
