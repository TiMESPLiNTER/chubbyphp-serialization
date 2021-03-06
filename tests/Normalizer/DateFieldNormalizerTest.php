<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Serialization\Normalizer;

use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Serialization\Normalizer\DateFieldNormalizer;
use Chubbyphp\Serialization\Normalizer\FieldNormalizerInterface;
use Chubbyphp\Serialization\Normalizer\NormalizerContextInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Serialization\Normalizer\DateFieldNormalizer
 */
class DateFieldNormalizerTest extends TestCase
{
    use MockByCallsTrait;

    public function testNormalizeField()
    {
        $object = $this->getObject();
        $object->setDate(new \DateTime('2017-01-01 22:00:00+01:00'));

        /** @var NormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(NormalizerContextInterface::class);

        $dateFieldNormalizer = new DateFieldNormalizer($this->getFieldNormalizer());

        self::assertSame(
            '2017-01-01T22:00:00+01:00',
            $dateFieldNormalizer->normalizeField(
                'date',
                $object,
                $context
            )
        );

        $error = error_get_last();

        error_clear_last();

        self::assertEquals(E_USER_DEPRECATED, $error['type']);
        self::assertEquals('Use Chubbyphp\Serialization\Normalizer\DateTimeFieldNormalizer instead', $error['message']);
    }

    public function testNormalizeWithValidDateString()
    {
        $object = $this->getObject();
        $object->setDate('2017-01-01 22:00:00+01:00');

        /** @var NormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(NormalizerContextInterface::class);

        $fieldNormalizer = new DateFieldNormalizer($this->getFieldNormalizer());

        self::assertSame(
            '2017-01-01T22:00:00+01:00',
            $fieldNormalizer->normalizeField(
                'date',
                $object,
                $context
            )
        );

        error_clear_last();
    }

    public function testNormalizeWithInvalidDateString()
    {
        $object = $this->getObject();
        $object->setDate('2017-01-01 25:00:00');

        /** @var NormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(NormalizerContextInterface::class);

        $fieldNormalizer = new DateFieldNormalizer($this->getFieldNormalizer());

        self::assertSame(
            '2017-01-01 25:00:00',
            $fieldNormalizer->normalizeField(
                'date',
                $object,
                $context
            )
        );

        error_clear_last();
    }

    public function testNormalizeWithNull()
    {
        $object = $this->getObject();

        /** @var NormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(NormalizerContextInterface::class);

        $fieldNormalizer = new DateFieldNormalizer($this->getFieldNormalizer());

        self::assertNull(
            $fieldNormalizer->normalizeField('date', $object, $context)
        );

        error_clear_last();
    }

    private function getObject()
    {
        return new class() {
            /**
             * @var \DateTime|string|null
             */
            private $date;

            /**
             * @return \DateTime|string|null
             */
            public function getDate()
            {
                return $this->date;
            }

            /**
             * @param \DateTime|string|null $date
             *
             * @return self
             */
            public function setDate($date): self
            {
                $this->date = $date;

                return $this;
            }
        };
    }

    /**
     * @return FieldNormalizerInterface
     */
    private function getFieldNormalizer(): FieldNormalizerInterface
    {
        /** @var FieldNormalizerInterface|MockObject $fieldNormalizer */
        $fieldNormalizer = $this->getMockBuilder(FieldNormalizerInterface::class)->getMockForAbstractClass();

        $fieldNormalizer->expects(self::any())->method('normalizeField')->willReturnCallback(
            function (string $path, $object) {
                return $object->getDate();
            }
        );

        return $fieldNormalizer;
    }
}
