<?php
use Engine\Category\Api\CategoryRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\TestFramework\Helper\Bootstrap;

/** @var CategoryRepositoryInterface $categoryRepository */
$categoryRepository = Bootstrap::getObjectManager()->get(CategoryRepositoryInterface::class);
try {
    $categoryRepository->deleteById(100);
} catch (NoSuchEntityException $e) {
    // Category doesn't exist
}
