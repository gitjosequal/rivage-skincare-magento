<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\Core\Helper;

/**
 * This class implements functionality of Magento\Framework\Serialize\SerializerInterface
 * to make it available on 2.0
 * Default serialization method depends on magento version:
 * 2.0 - native php serialization
 * 2.1 - native php serialization
 * 2.2 - json serialization
 */
class Serializer
{
    const METHOD_NATIVE = 0;

    const METHOD_JSON = 1;

    /**
     * @var int $serializationMethod
     */
    private $serializationMethod;
    /**
     * @var Metadata
     */
    private $metadataHelper;

    /**
     * Serializer constructor.
     * @param Metadata $metadataHelper
     */
    public function __construct(\MageWorkshop\Core\Helper\Metadata $metadataHelper)
    {
        $this->metadataHelper = $metadataHelper;
    }

    /**
     * Serialize data into string
     *
     * @param string|int|float|bool|array|null $data
     * @return string|bool
     * @throws \InvalidArgumentException
     */
    public function serialize($data)
    {
        return ($this->getSerializationMethod() === self::METHOD_JSON)
            ? $this->serializeJson($data)
            : $this->serializeNative($data);
    }

    /**
     * Unserialize the given string
     *
     * @param string $string
     * @return string|int|float|bool|array|null
     * @throws \InvalidArgumentException
     */
    public function unserialize($string)
    {
        return ($this->getSerializationMethod() === self::METHOD_JSON)
            ? $this->unserializeJson($string)
            : $this->unserializeNative($string);
    }

    /**
     * Serialize data into string using json_encode
     *
     * @param string|int|float|bool|array|null $data
     * @return string|bool
     * @throws \InvalidArgumentException
     */
    public function serializeJson($data)
    {
        $result = json_encode($data);
        if (false === $result) {
            throw new \InvalidArgumentException('Unable to serialize value.');
        }
        return $result;
    }

    /**
     * Unserialize the given string using json_decode
     *
     * @param string $string
     * @return string|int|float|bool|array|null
     * @throws \InvalidArgumentException
     */
    public function unserializeJson($string)
    {
        $result = json_decode($string, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Unable to unserialize value.');
        }
        return $result;
    }

    /**
     * Serialize data into string using native serialize method
     *
     * @param string|int|float|bool|array|null $data
     * @return string|bool
     * @throws \InvalidArgumentException
     */
    public function serializeNative($data)
    {
        if (is_resource($data)) {
            throw new \InvalidArgumentException('Unable to serialize value.');
        }
        return serialize($data);
    }

    /**
     * Unserialize the given string using native unserialize method
     *
     * @param string $string
     * @return string|int|float|bool|array|null
     * @throws \InvalidArgumentException
     */
    public function unserializeNative($string)
    {
        if (false === $string || null === $string || '' === $string) {
            throw new \InvalidArgumentException('Unable to unserialize value.');
        }
        set_error_handler(
            function () {
                restore_error_handler();
                throw new \InvalidArgumentException('Unable to unserialize value, string is corrupted.');
            },
            E_NOTICE
        );

        // The option "allowed_classes" parameter was added in PHP 7.0
        if (PHP_MAJOR_VERSION >= 7) {
            $result = unserialize($string, ['allowed_classes' => false]);
        } else {
            $result = unserialize($string);
        }
        restore_error_handler();
        return $result;
    }

    /**
     * @return int
     */
    private function getSerializationMethod()
    {
        if (null === $this->serializationMethod) {
            $this->serializationMethod = $this->metadataHelper->isMagentoVersionEqualOrGreater('2.2.0')
                ? self::METHOD_JSON
                : self::METHOD_NATIVE;
        }

        return $this->serializationMethod;
    }
}
