<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Config;

use Magento\Framework\Config\ConverterInterface;

class Converter implements ConverterInterface
{
    /**
     * These fields are stored in serialized json format in xml config file.
     * They need to be decoded and then reserialized
     * into current magento serialization format
     */
    const JSON_SERIALIZED_FIELDS = ['attribute_visual_settings', 'validate_rules'];

    /**
     * @var \MageWorkshop\Core\Helper\Serializer
     */
    private $serializer;

    /**
     * Converter constructor.
     * @param \MageWorkshop\Core\Helper\Serializer $serializer
     */
    public function __construct(
        \MageWorkshop\Core\Helper\Serializer $serializer
    ) {
        $this->serializer = $serializer;
    }

    /**
     * Convert dom node tree to array
     *
     * @param \DOMDocument $source
     * @return array
     * @throws \InvalidArgumentException
     */
    public function convert($source)
    {
        $output = [];
        $xpath = new \DOMXPath($source);
        /** @var \DOMNodeList $entities */
        $entities = $xpath->evaluate('/config/entity');

        /** @var \DOMElement $entity */
        foreach ($entities as $entity) {
            $entityType = $this->getAttributeValue($entity, 'type');

            if (!isset($output[$entityType])) {
                $output[$entityType] = [];
            }

            /** @var $childNode \DOMElement */
            foreach ($entity->childNodes as $childNode) {
                if ($childNode->nodeType !== XML_ELEMENT_NODE) {
                    continue;
                }

                if ($childNode->nodeName === 'attributes' && $childNode->hasChildNodes()) {
                    foreach ($childNode->getElementsByTagName('attribute') as $attributeNode) {
                        if (!isset($output[$entityType]['attributes'])) {
                            $output[$entityType]['attributes'] = [];
                        }
                        $attributeData = $this->convertChild($attributeNode);
                        $inputType = $this->getAttributeValue($attributeNode, 'frontend-input');
                        $attributeData['frontend_input'] = $inputType;
                        $output[$entityType]['attributes'][$inputType] = $attributeData;
                    }
                } else {
                    $output[$entityType][$childNode->nodeName] = $childNode->textContent;
                }
            }
        }

        $defaultAttributes = $output['default']['attributes'];

        foreach ($output as $entityType => $entityConfig) {
            if ($entityType !== 'default') {
                if (!isset($entityConfig['attributes'])) {
                    $entityConfig['attributes'] = [];
                }

                $attributes = array_merge($defaultAttributes, $entityConfig['attributes']);
                uasort($attributes, function ($a, $b) {
                    if ($a['position'] == $b['position']) {
                        return 0;
                    }
                    return ($a['position'] < $b['position']) ? -1 : 1;
                });
                $output[$entityType]['attributes'] = $attributes;
            }
        }
        return $output;
    }

    /**
     * Get attribute value
     *
     * @param \DOMNode $input
     * @param string $attributeName
     * @param mixed $default
     * @return null|string
     */
    private function getAttributeValue(\DOMNode $input, $attributeName, $default = null)
    {
        $node = $input->attributes->getNamedItem($attributeName);
        return $node ? $node->nodeValue : $default;
    }

    /**
     * Convert child from dom to array
     *
     * @param \DOMNode $childNode
     * @return array
     * @throws \InvalidArgumentException
     */
    private function convertChild(\DOMNode $childNode)
    {
        $data = [];
        switch ($childNode->nodeName) {
            case 'attribute':
                /** @var $subscription \DOMNode */
                foreach ($childNode->childNodes as $attribute) {
                    $nodeName = $attribute->nodeName;
                    if ($attribute->nodeType !== XML_ELEMENT_NODE) {
                        continue;
                    }
                    if (!empty($attribute->textContent)) {
                        if (in_array($nodeName, self::JSON_SERIALIZED_FIELDS, true)) {
                            $data[$nodeName] = $this->serializer->serialize(json_decode($attribute->textContent));
                        } else {
                            $data[$nodeName] = $attribute->textContent;
                        }
                    }
                }
                break;
        }
        return $data;
    }
}
