<?php
declare(strict_types=1);

namespace Engine\Location\Controller\Adminhtml\Country;

use Engine\Location\Api\CountryRepositoryInterface;
use Engine\Location\Api\Data\CountryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @author naydav <valeriy.nayda@gmail.com>
 */
class Edit extends Action
{
    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Engine_Location::location_country';

    /**
     * @var CountryRepositoryInterface
     */
    private $countryRepository;

    /**
     * @param Context $context
     * @param CountryRepositoryInterface $countryRepository
     */
    public function __construct(
        Context $context,
        CountryRepositoryInterface $countryRepository
    ) {
        parent::__construct($context);
        $this->countryRepository = $countryRepository;
    }

    /**
     * @inheritdoc
     */
    public function execute(): ResultInterface
    {
        $countryId = $this->getRequest()->getParam(CountryInterface::COUNTRY_ID);
        try {
            $country = $this->countryRepository->get((int)$countryId);

            /** @var Page $result */
            $result = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
            $result->setActiveMenu('Engine_Location::location_country')
                ->addBreadcrumb(__('Edit Country'), __('Edit Country'));
            $result->getConfig()
                ->getTitle()
                ->prepend(
                    __('Edit Country: %name', ['name' => $country->getName()])
                );
        } catch (NoSuchEntityException $e) {
            /** @var Redirect $result */
            $result = $this->resultRedirectFactory->create();
            $this->messageManager->addErrorMessage(
                __('Country with id "%id" does not exist.', ['id' => $countryId])
            );
            $result->setPath('*/*');
        }
        return $result;
    }
}
