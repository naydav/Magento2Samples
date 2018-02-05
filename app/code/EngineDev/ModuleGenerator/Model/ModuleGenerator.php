<?php
declare(strict_types=1);

namespace EngineDev\ModuleGenerator\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem\Driver\File;

/**
 * @author naydav <valeriy.nayda@gmail.com>
 */
class ModuleGenerator
{
    /**
     * @var File
     */
    private $filesystemDriver;

    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * @var bool
     */
    private $forceGeneration;

    /**
     * @var array
     */
    private $generatorsData;

    /**
     * @var array
     */
    private $modulesData;

    /**
     * @param File $filesystemDriver
     * @param DirectoryList $directoryList
     * @param bool $forceGeneration
     * @param array $generatorsData
     * @param array $modulesData
     */
    public function __construct(
        File $filesystemDriver,
        DirectoryList $directoryList,
        $forceGeneration = true,
        array $generatorsData = [],
        array $modulesData = []
    ) {
        $this->filesystemDriver = $filesystemDriver;
        $this->directoryList = $directoryList;
        $this->forceGeneration = $forceGeneration;
        $this->generatorsData = $generatorsData;
        $this->modulesData = $modulesData;
    }

    /**
     * @param string|null $module
     * @return void
     * @throws LocalizedException
     */
    public function generate(string $module = null)
    {
        if (null !== $module) {
            $moduleData = $this->getModuleData($module);
            $this->generateModule($moduleData);
        } else {
            foreach ($this->modulesData as $moduleData) {
                $this->generateModule($moduleData);
            }
        }
    }

    /**
     * @param string $module
     * @return array
     * @throws LocalizedException
     */
    private function getModuleData(string $module)
    {
        if (!isset($this->modulesData[$module])) {
            throw new LocalizedException(__('Module definition for "%1" is not found', $module));
        }
        return $this->modulesData[$module];
    }

    /**
     * @param array $moduleData
     * @return void
     */
    private function generateModule(array $moduleData)
    {
        $moduleDir = $this->getModuleDir($moduleData);

        if (!empty($moduleData['generators'])) {
            foreach ($moduleData['generators'] as $generatorName) {
                $generatorData = $this->getGeneratorData($generatorName);
                $this->generateFile($moduleDir, $generatorData, $moduleData);
            }
        }

        if (!empty($moduleData['entities'])) {
            foreach ($moduleData['entities'] as $entityData) {
                if (!empty($entityData['entity_generators'])) {
                    foreach ($entityData['entity_generators'] as $generatorName) {
                        $generatorData = $this->getGeneratorData($generatorName);
                        $entityData = array_merge($moduleData, $entityData);
                        $this->generateFile($moduleDir, $generatorData, $entityData, true);
                    }
                }
            }
        }
    }

    /**
     * @param array $moduleDefinition
     * @return string
     */
    private function getModuleDir(array $moduleDefinition)
    {
        $appDir = $this->directoryList->getPath(DirectoryList::APP);

        return $appDir . '/code/' . $moduleDefinition['namespace_prefix'] . '/' . $moduleDefinition['name_camelcase'];
    }

    /**
     * @param string $generator
     * @return array
     * @throws LocalizedException
     */
    private function getGeneratorData(string $generator)
    {
        if (!isset($this->generatorsData[$generator])) {
            throw new LocalizedException(__('Generator definition for "%1" is not found', $generator));
        }
        return $this->generatorsData[$generator];
    }

    /**
     * @param string $moduleDir
     * @param array $generatorData
     * @param array $data
     * @param bool $needPathResolving
     * @return void
     */
    private function generateFile(string $moduleDir, array $generatorData, array $data, $needPathResolving = false)
    {
        $destinationDir = $moduleDir . '/' . $generatorData['destination_dir'];
        if (true === $needPathResolving) {
            $destinationDir = $this->resolvePathName($destinationDir, $data);
        }

        $destinationFile = $destinationDir . '/' . $generatorData['destination_file'];
        if (true === $needPathResolving) {
            $destinationFile = $this->resolvePathName($destinationFile, $data);
        }

        if ($this->forceGeneration || !$this->filesystemDriver->isExists($destinationFile)) {
            if (!$this->filesystemDriver->isDirectory($destinationDir)) {
                $this->filesystemDriver->createDirectory($destinationDir);
            }
            $fileContent = $this->generateFileContent($this->getTemplatePath($generatorData['template']), $data);
            $this->filesystemDriver->filePutContents($destinationFile, $fileContent);
        }
    }

    /**
     * @param string $path
     * @param array $data
     * @return string
     */
    private function resolvePathName(string $path, array $data)
    {
        $path = str_replace(
                [
                    '%entity_class_name%',
                    '%entity_class_name_lower%',
                    '%entity_name_lower%',
                    '%entities_name_lower%',
                    '%module_name_lower%',
                    '%entity_ui_name%',
                ],
                [
                    $data['entity_class_name'],
                    strtolower($data['entity_class_name']),
                    strtolower($data['entity_name']),
                    $data['entities_name'],
                    strtolower($data['name']),
                    $data['entity_ui_name'],
                ],
                $path
            );
        return $path;
    }

    /**
     * @param string $template
     * @return string
     */
    private function getTemplatePath($template)
    {
        $appDir = $this->directoryList->getPath(DirectoryList::APP);

        return $appDir . '/code/' . $template;
    }

    /**
     * @param string $template
     * @param array $data
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    private function generateFileContent(string $template, array $data)
    {
        \ob_start();
        include $template;
        $content = \ob_get_clean();
        return $content;
    }
}
