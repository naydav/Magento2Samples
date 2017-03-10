<?php
namespace Engine\Location\Controller\Adminhtml\Region;

use Engine\Location\Api\Data\RegionInterface;
use Engine\Location\Api\Data\RegionInterfaceFactory;
use Engine\Location\Api\RegionRepositoryInterface;
use Engine\Location\Model\Region\RegionValidatorInterface;
use Engine\MagentoFix\Exception\ValidatorException;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class Validate extends Action
{
    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Engine_Location::location_region';

    /**
     * @var RegionInterfaceFactory
     */
    private $regionFactory;

    /**
     * @var RegionRepositoryInterface
     */
    private $regionRepository;

    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @var RegionValidatorInterface
     */
    private $regionValidator;

    /**
     * @param Context $context
     * @param RegionInterfaceFactory $regionFactory
     * @param RegionRepositoryInterface $regionRepository
     * @param HydratorInterface $hydrator
     * @param RegionValidatorInterface $regionValidator
     */
    public function __construct(
        Context $context,
        RegionInterfaceFactory $regionFactory,
        RegionRepositoryInterface $regionRepository,
        HydratorInterface $hydrator,
        RegionValidatorInterface $regionValidator
    ) {
        parent::__construct($context);
        $this->regionFactory = $regionFactory;
        $this->regionRepository = $regionRepository;
        $this->hydrator = $hydrator;
        $this->regionValidator = $regionValidator;
    }

    /**
     * @return Json
     */
    public function execute()
    {
        $errorMessages = [];
        $request = $this->getRequest();
        $requestData = $this->getRequest()->getParam('general');

        if ($request->isXmlHttpRequest() && $request->isPost() && $requestData) {
            $regionId = !empty($requestData[RegionInterface::REGION_ID])
                ? $requestData[RegionInterface::REGION_ID] : null;

            try {
                if ($regionId) {
                    $region = $this->regionRepository->get($regionId);
                } else {
                    /** @var RegionInterface $region */
                    $region = $this->regionFactory->create();
                }
                $region = $this->hydrator->hydrate($region, $requestData);
                $this->regionValidator->validate($region);
            } catch (NoSuchEntityException $e) {
                $errorMessages[] = __('The Region does not exist.');
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
