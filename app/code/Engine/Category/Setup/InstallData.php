<?php
namespace Engine\Category\Setup;

use Engine\Category\Api\Data\CategoryInterface;
use Engine\Category\Api\Data\CategoryInterfaceFactory;
use Engine\Category\Api\CategoryRepositoryInterface;
use Engine\Category\Api\RootCategoryIdProviderInterface;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class InstallData implements InstallDataInterface
{
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
     * @var RootCategoryIdProviderInterface
     */
    private $rootCategoryIdProvider;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param CategoryInterfaceFactory $categoryFactory
     * @param CategoryRepositoryInterface $categoryRepository
     * @param HydratorInterface $hydrator
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        CategoryInterfaceFactory $categoryFactory,
        CategoryRepositoryInterface $categoryRepository,
        HydratorInterface $hydrator,
        RootCategoryIdProviderInterface $rootCategoryIdProvider,
        StoreManagerInterface $storeManager
    ) {
        $this->categoryFactory = $categoryFactory;
        $this->categoryRepository = $categoryRepository;
        $this->hydrator = $hydrator;
        $this->rootCategoryIdProvider = $rootCategoryIdProvider;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->storeManager->setCurrentStore(Store::DEFAULT_STORE_ID);
        /** @var CategoryInterface $category */
        $category = $this->categoryFactory->create();
        $category = $this->hydrator->hydrate($category, [
            CategoryInterface::CATEGORY_ID => $this->rootCategoryIdProvider->provide(),
            CategoryInterface::PARENT_ID => null,
            CategoryInterface::TITLE => 'Root',
            CategoryInterface::URL_KEY => 'root',
            CategoryInterface::IS_ENABLED => true,
            CategoryInterface::POSITION => 0,
        ]);
        $this->categoryRepository->save($category);
    }
}
