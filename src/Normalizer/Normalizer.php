<?php

declare(strict_types=1);

namespace Chubbyphp\Serialization\Normalizer;

use Chubbyphp\Serialization\Mapping\NormalizationFieldMappingInterface;
use Chubbyphp\Serialization\Mapping\NormalizationLinkMappingInterface;
use Chubbyphp\Serialization\Mapping\NormalizationObjectMappingInterface;
use Chubbyphp\Serialization\SerializerLogicException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class Normalizer implements NormalizerInterface
{
    /**
     * @var NormalizerObjectMappingRegistryInterface
     */
    private $normalizerObjectMappingRegistry;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param NormalizerObjectMappingRegistryInterface $normalizerObjectMappingRegistry
     * @param LoggerInterface|null                     $logger
     */
    public function __construct(
        NormalizerObjectMappingRegistryInterface $normalizerObjectMappingRegistry,
        LoggerInterface $logger = null
    ) {
        $this->normalizerObjectMappingRegistry = $normalizerObjectMappingRegistry;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @param object                          $object
     * @param NormalizerContextInterface|null $context
     * @param string                          $path
     *
     * @return array
     */
    public function normalize(
        $object,
        NormalizerContextInterface $context = null,
        string $path = ''
    ): array {
        $this->validateDataType($object, $path);

        $context = $context ?? NormalizerContextBuilder::create()->getContext();

        $class = is_object($object) ? get_class($object) : $object;
        $objectMapping = $this->getObjectMapping($class);

        $fieldMappings = $objectMapping->getNormalizationFieldMappings($path);

        $data = $this->getFieldsByFieldNormalizationMappings($context, $fieldMappings, $path, $object);

        $embeddedMappings = $objectMapping->getNormalizationEmbeddedFieldMappings($path);
        $embedded = $this->getFieldsByFieldNormalizationMappings($context, $embeddedMappings, $path, $object);

        $linkMappings = $objectMapping->getNormalizationLinkMappings($path);
        $links = $this->getLinksByLinkNormalizationMappings($context, $linkMappings, $path, $object);

        if ([] !== $embedded) {
            $data['_embedded'] = $embedded;
        }

        if ([] !== $links) {
            $data['_links'] = $links;
        }

        if (null !== $type = $objectMapping->getNormalizationType()) {
            $data['_type'] = $type;
        }

        return $data;
    }

    /**
     * @param object $object
     * @param string $path
     *
     * @throws SerializerLogicException
     */
    private function validateDataType($object, string $path)
    {
        if (!is_object($object)) {
            $exception = SerializerLogicException::createWrongDataType(gettype($object), $path);

            $this->logger->error('serialize: {exception}', ['exception' => $exception->getMessage()]);

            throw $exception;
        }
    }

    /**
     * @param string $class
     *
     * @return NormalizationObjectMappingInterface
     *
     * @throws SerializerLogicException
     */
    private function getObjectMapping(string $class): NormalizationObjectMappingInterface
    {
        try {
            return $this->normalizerObjectMappingRegistry->getObjectMapping($class);
        } catch (SerializerLogicException $exception) {
            $this->logger->error('serialize: {exception}', ['exception' => $exception->getMessage()]);

            throw $exception;
        }
    }

    /**
     * @param NormalizerContextInterface           $context
     * @param NormalizationFieldMappingInterface[] $normalizationFieldMappings
     * @param string                               $path
     * @param object                               $object
     *
     * @return array
     */
    private function getFieldsByFieldNormalizationMappings(
        NormalizerContextInterface $context,
        array $normalizationFieldMappings,
        string $path,
        $object
    ): array {
        $data = [];
        foreach ($normalizationFieldMappings as $normalizationFieldMapping) {
            if (!$this->isWithinGroup($context, $normalizationFieldMapping)) {
                continue;
            }

            $fieldNormalizer = $normalizationFieldMapping->getFieldNormalizer();

            $name = $normalizationFieldMapping->getName();

            $subPath = $this->getSubPathByName($path, $name);

            $this->logger->info('serialize: path {path}', ['path' => $subPath]);

            $data[$name] = $fieldNormalizer->normalizeField($subPath, $object, $context, $this);
        }

        return $data;
    }

    /**
     * @param NormalizerContextInterface          $context
     * @param NormalizationLinkMappingInterface[] $normalizationLinkMappings
     * @param string                              $path
     * @param object                              $object
     *
     * @return array
     */
    private function getLinksByLinkNormalizationMappings(
        NormalizerContextInterface $context,
        array $normalizationLinkMappings,
        string $path,
        $object
    ): array {
        $links = [];
        foreach ($normalizationLinkMappings as $normalizationLinkMapping) {
            if (!$this->isWithinGroup($context, $normalizationLinkMapping)) {
                continue;
            }

            $linkNormalizer = $normalizationLinkMapping->getLinkNormalizer();

            if (null === $link = $linkNormalizer->normalizeLink($path, $object, $context)) {
                continue;
            }

            $links[$normalizationLinkMapping->getName()] = $link;
        }

        return $links;
    }

    /**
     * @param NormalizerContextInterface                                           $context
     * @param NormalizationFieldMappingInterface|NormalizationLinkMappingInterface $mapping
     *
     * @return bool
     */
    private function isWithinGroup(NormalizerContextInterface $context, $mapping): bool
    {
        if ([] === $groups = $context->getGroups()) {
            return true;
        }

        foreach ($mapping->getGroups() as $group) {
            if (in_array($group, $groups, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $path
     * @param string $name
     *
     * @return string
     */
    private function getSubPathByName(string $path, string $name): string
    {
        return '' === $path ? $name : $path.'.'.$name;
    }
}
