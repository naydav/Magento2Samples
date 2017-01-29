<?php
namespace Engine\Category\Ui\Component\Listing\Column;

use Engine\Category\Api\Data\CategoryInterface;
use Engine\Category\Model\Category\Source\CategorySource;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @spi
 */
class ParentCategory extends Column
{
    /**
     * @var CategorySource
     */
    private $categorySource;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param CategorySource $categorySource
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        CategorySource $categorySource,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->categorySource = $categorySource;
    }


    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (!empty($dataSource['data']['items'])) {
            $categoryMap = array_column($this->categorySource->toOptionArray(), 'label', 'value');
            foreach ($dataSource['data']['items'] as &$row) {
                if (isset($row[CategoryInterface::PARENT_ID])) {
                    $row[CategoryInterface::PARENT_ID] = $categoryMap[$row[CategoryInterface::PARENT_ID]];
                }
            }
            unset($row);
        }
        return $dataSource;
    }
}
