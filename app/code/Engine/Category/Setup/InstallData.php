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
     * @param CategoryInterfaceFactory $categoryFactory
     * @param CategoryRepositoryInterface $categoryRepository
     * @param HydratorInterface $hydrator
     */
    public function __construct(
        CategoryInterfaceFactory $categoryFactory,
        CategoryRepositoryInterface $categoryRepository,
        HydratorInterface $hydrator,
        RootCategoryIdProviderInterface $rootCategoryIdProvider
    ) {
        $this->categoryFactory = $categoryFactory;
        $this->categoryRepository = $categoryRepository;
        $this->hydrator = $hydrator;
        $this->rootCategoryIdProvider = $rootCategoryIdProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var CategoryInterface $category */
        $category = $this->categoryFactory->create();
        $category = $this->hydrator->hydrate($category, [
            CategoryInterface::CATEGORY_ID => $this->rootCategoryIdProvider->get(),
            CategoryInterface::PARENT_ID => null,
            CategoryInterface::TITLE => 'Root',
            CategoryInterface::URL_KEY => 'root',
            CategoryInterface::IS_ENABLED => true,
            CategoryInterface::POSITION => 0,
        ]);
        $this->categoryRepository->save($category);
    }
}
