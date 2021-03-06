<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Serialization\Mapping;

use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Serialization\Mapping\NormalizationFieldMappingBuilder;
use Chubbyphp\Serialization\Normalizer\CallbackFieldNormalizer;
use Chubbyphp\Serialization\Normalizer\DateTimeFieldNormalizer;
use Chubbyphp\Serialization\Normalizer\FieldNormalizer;
use Chubbyphp\Serialization\Normalizer\FieldNormalizerInterface;
use Chubbyphp\Serialization\Normalizer\Relation\EmbedManyFieldNormalizer;
use Chubbyphp\Serialization\Normalizer\Relation\EmbedOneFieldNormalizer;
use Chubbyphp\Serialization\Normalizer\Relation\ReferenceManyFieldNormalizer;
use Chubbyphp\Serialization\Normalizer\Relation\ReferenceOneFieldNormalizer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Serialization\Mapping\NormalizationFieldMappingBuilder
 */
class NormalizationFieldMappingBuilderTest extends TestCase
{
    use MockByCallsTrait;

    public function testGetDefaultMapping()
    {
        $fieldMapping = NormalizationFieldMappingBuilder::create('name')->getMapping();

        self::assertSame('name', $fieldMapping->getName());
        self::assertSame([], $fieldMapping->getGroups());
        self::assertInstanceOf(FieldNormalizer::class, $fieldMapping->getFieldNormalizer());
    }

    public function testGetDefaultMappingForCallback()
    {
        $fieldMapping = NormalizationFieldMappingBuilder::createCallback('name', function () {})->getMapping();

        self::assertSame('name', $fieldMapping->getName());
        self::assertSame([], $fieldMapping->getGroups());
        self::assertInstanceOf(CallbackFieldNormalizer::class, $fieldMapping->getFieldNormalizer());
    }

    public function testGetDefaultMappingForDateTime()
    {
        $fieldMapping = NormalizationFieldMappingBuilder::createDateTime('name')->getMapping();

        self::assertSame('name', $fieldMapping->getName());
        self::assertSame([], $fieldMapping->getGroups());
        self::assertInstanceOf(DateTimeFieldNormalizer::class, $fieldMapping->getFieldNormalizer());
    }

    public function testGetDefaultMappingForDateTimeWithFormat()
    {
        $fieldMapping = NormalizationFieldMappingBuilder::createDateTime('name', \DateTime::ATOM)->getMapping();

        /** @var DateTimeFieldNormalizer $fieldNormalizer */
        $fieldNormalizer = $fieldMapping->getFieldNormalizer();

        self::assertSame('name', $fieldMapping->getName());
        self::assertSame([], $fieldMapping->getGroups());
        self::assertInstanceOf(DateTimeFieldNormalizer::class, $fieldNormalizer);

        $reflection = new \ReflectionProperty($fieldNormalizer, 'format');
        $reflection->setAccessible(true);

        self::assertSame(\DateTime::ATOM, $reflection->getValue($fieldNormalizer));
    }

    public function testGetDefaultMappingForEmbedMany()
    {
        $fieldMapping = NormalizationFieldMappingBuilder::createEmbedMany('name')->getMapping();

        self::assertSame('name', $fieldMapping->getName());
        self::assertSame([], $fieldMapping->getGroups());
        self::assertInstanceOf(EmbedManyFieldNormalizer::class, $fieldMapping->getFieldNormalizer());
    }

    public function testGetDefaultMappingForEmbedOne()
    {
        $fieldMapping = NormalizationFieldMappingBuilder::createEmbedOne('name')->getMapping();

        self::assertSame('name', $fieldMapping->getName());
        self::assertSame([], $fieldMapping->getGroups());
        self::assertInstanceOf(EmbedOneFieldNormalizer::class, $fieldMapping->getFieldNormalizer());
    }

    public function testGetDefaultMappingForReferenceMany()
    {
        $fieldMapping = NormalizationFieldMappingBuilder::createReferenceMany('name')->getMapping();

        self::assertSame('name', $fieldMapping->getName());
        self::assertSame([], $fieldMapping->getGroups());
        self::assertInstanceOf(ReferenceManyFieldNormalizer::class, $fieldMapping->getFieldNormalizer());
    }

    public function testGetDefaultMappingForReferenceOne()
    {
        $fieldMapping = NormalizationFieldMappingBuilder::createReferenceOne('name')->getMapping();

        self::assertSame('name', $fieldMapping->getName());
        self::assertSame([], $fieldMapping->getGroups());
        self::assertInstanceOf(ReferenceOneFieldNormalizer::class, $fieldMapping->getFieldNormalizer());
    }

    public function testGetMapping()
    {
        /** @var FieldNormalizerInterface|MockObject $fieldNormalizer */
        $normalizer = $this->getMockByCalls(FieldNormalizerInterface::class);

        $fieldMapping = NormalizationFieldMappingBuilder::create('name')
            ->setGroups(['group1'])
            ->setFieldNormalizer($normalizer)
            ->getMapping();

        self::assertSame('name', $fieldMapping->getName());
        self::assertSame(['group1'], $fieldMapping->getGroups());
        self::assertSame($normalizer, $fieldMapping->getFieldNormalizer());
    }
}
