<?php
namespace XP\OrderPdf\Model\Config;

use XP\OrderPdf\Model\ConfigInterface;

/**
 * Collects the configurations
 */
class ConfigPool
{
    /**
     * @var array|ConfigInterface[]
     */
    private array $configurations;

    /**
     * @param array $configurations
     */
    public function __construct(array $configurations)
    {
        $this->configurations = $configurations;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        $config = [];
        foreach ($this->configurations as $key => $configuration) {
            $config [$key]= $configuration->getPDFConfig();
        }
        return $config;
    }
}
