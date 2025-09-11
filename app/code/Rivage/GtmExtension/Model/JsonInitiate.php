<?php
/**
 * Copyright © Rivage(info@rivage.com) All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Rivage\GtmExtension\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Rivage\GtmExtension\Ga4JsonExport\ApiConfig as ApiCore;

/**
 * Class \Rivage\GtmExtension\Model\JsonInitiate
 */
class JsonInitiate extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var string
     */
    protected $containerId;

    /**
     * @var ApiCore
     */
    protected $apiCore;

    /**
     * @var string
     */
    protected $accountId;

    /**
     * @var string
     */
    protected $measurementId;

    /**
     * @var string
     */
    protected $publicId;

     /**
      * @var integer
      */
    protected $fingerprint;

    /**
     * @var string
     */
    protected $exportFileName;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param Filesystem $filesystem
     * @param ApiCore $apiCore
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Filesystem $filesystem,
        ApiCore $apiCore
    ) {
        parent::__construct($context, $registry);
        $this->filesystem = $filesystem;
        $this->apiCore = $apiCore;
        $this->exportFileName = 'ga4_gtm_ga4' . DIRECTORY_SEPARATOR . 'gtm.json';
    }

    /**
     * Generates the JSON content for GtmExtension integration configuration.
     *
     * @param string $accountId
     * @param string $containerId
     * @param string $measurementId
     * @param string $publicId
     * @return string
     */
    public function generateGA4Json(
        $accountId,
        $containerId,
        $measurementId,
        $publicId
    ) {
        $this->fingerprint = time();
        $this->accountId = $accountId;
        $this->containerId = $containerId;
        $this->measurementId = $measurementId;
        $this->publicId = $publicId;

        $jsonvar = $this->generateJsonVariables();
        $jsontrigger = $this->generateJsonTriggers();
        $jsontags = $this->generateJsonTags($jsontrigger);

        // @codingStandardsIgnoreStart
        // Generate export options
        $exportOptions = $this->generateExportOptions($jsonvar, $jsontrigger, $jsontags);

        // Define the final output
        $output = $this->generateOutput($exportOptions);

        // Write the JSON to the file
        $this->writeJsonToFile($output);

        return true;
        // @codingStandardsIgnoreEnd
    }

    /**
     * Generate the export options array.
     *
     * @param Json $jsonvar
     * @param Json $jsontrigger
     * @param Json $jsontags
     * @return array
     */
    private function generateExportOptions($jsonvar, $jsontrigger, $jsontags)
    {
        return [
            "path" => "accounts/$this->accountId/containers/$this->containerId/versions/0",
            "accountId" => $this->accountId,
            "containerId" => $this->containerId,
            "containerVersionId" => "0",
            "container" => $this->generateContainerOptions(),
            "builtInVariable" => $this->generateBuiltInVariables(),
            "variable" => array_values($jsonvar),
            "trigger" => array_values($jsontrigger),
            "tag" => array_values($jsontags),
            "fingerprint" => $this->fingerprint,
        ];
    }

    // @codingStandardsIgnoreStart

    /**
     * Generate the container options array.
     *
     * @return array
     */
    private function generateContainerOptions()
    {
        return [
            "path" => "accounts/$this->accountId/containers/$this->containerId",
            "accountId" => $this->accountId,
            "containerId" => $this->containerId,
            "name" => "Rivage_GtmExtension_JsonExport",
            "publicId" => $this->publicId,
            "usageContext" => ["WEB"],
            "fingerprint" => $this->fingerprint,
            "tagManagerUrl" => "https://tagmanager.google.com/#/container/accounts/$this->accountId/containers/$this->containerId/workspaces?apiLink=container",
        ];
    }
    // @codingStandardsIgnoreEnd

    /**
     * Generate built-in variables for the JSON.
     *
     * @return array
     */
    private function generateBuiltInVariables()
    {
        return [
            [
                "accountId" => $this->accountId,
                "containerId" => $this->containerId,
                "type" => "PAGE_URL",
                "name" => "Page URL"
            ],
            [
                "accountId" => $this->accountId,
                "containerId" => $this->containerId,
                "type" => "PAGE_HOSTNAME",
                "name" => "Page Hostname"
            ],
            [
                "accountId" => $this->accountId,
                "containerId" => $this->containerId,
                "type" => "PAGE_PATH",
                "name" => "Page Path"
            ],
            [
                "accountId" => $this->accountId,
                "containerId" => $this->containerId,
                "type" => "REFERRER",
                "name" => "Referrer"
            ],
            [
                "accountId" => $this->accountId,
                "containerId" => $this->containerId,
                "type" => "EVENT",
                "name" => "Event"
            ],
            // Add more built-in variables as needed...
        ];
    }

    // @codingStandardsIgnoreStart

    /**
     * Generate the final output array.
     *
     * @param array $exportOptions
     * @return array
     */
    private function generateOutput($exportOptions)
    {
        return [
            "exportFormatVersion" => 2,
            "exportTime" => date("Y-m-d h:i:s"),
            "containerVersion" => $exportOptions,
            "fingerprint" => $this->fingerprint,
            "tagManagerUrl" => "https://tagmanager.google.com/#/versions/accounts/$this->accountId/containers/$this->containerId/versions/0?apiLink=version",
        ];
    }
    // @codingStandardsIgnoreEnd

    /**
     * Write the JSON output to a file.
     *
     * @param array $output
     * @return void
     */
    private function writeJsonToFile($output)
    {
        $exportPath = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $exportPath->writeFile($this->exportFileName, json_encode($output, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    }

    /**
     * Retrieves the generated JSON content for GA4 integration configuration.
     *
     * @return string The JSON content as a string.
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getGeneratedJsonContent()
    {
        $exportPath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        return $exportPath->readFile($this->exportFileName);
    }

    /**
     * Retrieves the variables configuration for generating JSON.
     *
     * @return array
     */
    protected function generateJsonVariables()
    {
        $variablesToExport = $this->apiCore->getVariablesList($this->measurementId);

        $variableIndex = 1;
        foreach ($variablesToExport as &$exportedVariable) {
            $this->normalizeParameterTypes($exportedVariable);
            $this->setCommonAttributes($exportedVariable, $variableIndex);

            $variableIndex++;
        }

        return $variablesToExport;
    }

    /**
     * Normalize parameter types to uppercase.
     *
     * @param array $variable The variable options.
     */
    private function normalizeParameterTypes(&$variable)
    {
        if (isset($variable['parameter'])) {
            foreach ($variable['parameter'] as &$paramOptions) {
                if (isset($paramOptions['type'])) {
                    $paramOptions['type'] = strtoupper($paramOptions['type']);
                }
            }
        }
    }

    /**
     * Set common attributes for variables.
     *
     * @param array $variable The variable options.
     * @param int $index The variable index.
     */
    private function setCommonAttributes(&$variable, $index)
    {
        $variable['accountId'] = $this->accountId;
        $variable['containerId'] = $this->containerId;
        $variable['variableId'] = $index;
        $variable['fingerprint'] = $this->fingerprint;
        $variable['formatValue'] = new \stdClass();
    }

    // @codingStandardsIgnoreStart
    /**
     * Retrieves the triggers configuration for generating JSON.
     *
     * @return array
     */
    protected function generateJsonTriggers()
    {
        $triggersToCreate = $this->apiCore->getTriggersList();
        $triggerId = 1;

        array_walk_recursive($triggersToCreate, function (&$value, $key) {
            if ($key === 'type') {
                $value = strtoupper(preg_replace('/(.)([A-Z])/', '$1_$2', $value));
            } elseif ($key === 'parameter' && isset($value['type'])) {
                $value['type'] = strtoupper($value['type']);
            }
        });

        foreach ($triggersToCreate as &$triggerOptions) {
            $triggerOptions['accountId'] = $this->accountId;
            $triggerOptions['containerId'] = $this->containerId;
            $triggerOptions['triggerId'] = $triggerId;
            $triggerOptions['fingerprint'] = $this->fingerprint;
            $triggerId++;
        }

        return $triggersToCreate;
    }

    /**
     * Retrieves the tags configuration for generating JSON.
     *
     * @param string $triggers
     * @return array
     */

    public function generateJsonTags($triggers)
    {
        $triggersMap = $this->createTriggersMap($triggers);
        $tagsToCreate = $this->apiCore->getTagsList($triggersMap);

        $tagId = 1;
        foreach ($tagsToCreate as $tagName => &$tagOptions) {
            $this->processTagOptions($tagOptions);
            $tagOptions['accountId'] = $this->accountId;
            $tagOptions['containerId'] = $this->containerId;
            $tagOptions['tagId'] = $tagId;
            $tagOptions['fingerprint'] = $this->fingerprint;
            $tagId += 1;
        }

        return $tagsToCreate;
    }

    private function createTriggersMap($triggers)
    {
        $triggersMap = [];
        foreach ($triggers as $trigger) {
            $triggersMap[$trigger['name']] = $trigger['triggerId'];
        }
        return $triggersMap;
    }

    private function processTagOptions(&$tagOptions)
    {
        if (isset($tagOptions['parameter'])) {
            $this->processParameters($tagOptions['parameter']);
        }

        if (isset($tagOptions['tagFiringOption'])) {
            $tagOptions['tagFiringOption'] = $this->formatTagFiringOption($tagOptions['tagFiringOption']);
        }
    }

    private function processParameters(&$parameters)
    {
        foreach ($parameters as $key => &$paramOptions) {
            if (empty($paramOptions)) {
                unset($parameters[$key]);
                continue;
            }

            if (isset($paramOptions['type'])) {
                $paramOptions['type'] = strtoupper($paramOptions['type']);
            }

            if (isset($paramOptions['list'])) {
                $this->processListOptions($paramOptions['list']);
            }
        }
    }

    private function processListOptions(&$listOptions)
    {
        foreach ($listOptions as &$listOption) {
            if (isset($listOption['type'])) {
                $listOption['type'] = strtoupper($listOption['type']);
            }

            foreach ($listOption['map'] as &$mapOptions) {
                if (isset($mapOptions['type'])) {
                    $mapOptions['type'] = strtoupper($mapOptions['type']);
                }
            }
        }
    }

    private function formatTagFiringOption($tagFiringOption)
    {
        return strtoupper(preg_replace('/(.)([A-Z])/', '$1_$2', $tagFiringOption));
    }

    // @codingStandardsIgnoreEnd
}
