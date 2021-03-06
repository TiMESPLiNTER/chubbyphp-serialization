<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Serialization\Normalizer\Relation;

use Chubbyphp\Serialization\Accessor\AccessorInterface;
use Chubbyphp\Serialization\Normalizer\NormalizerContextInterface;
use Chubbyphp\Serialization\Normalizer\NormalizerInterface;
use Chubbyphp\Serialization\Normalizer\Relation\EmbedManyFieldNormalizer;
use Chubbyphp\Serialization\SerializerLogicException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Serialization\Normalizer\Relation\EmbedManyFieldNormalizer
 */
class EmbedManyFieldNormalizerTest extends TestCase
{
    public function testNormalizeMissingNormalizer()
    {
        $this->expectException(SerializerLogicException::class);
        $this->expectExceptionMessage('There is no normalizer at path: "children"');

        $fieldNormalizer = new EmbedManyFieldNormalizer($this->getAccessor());

        $fieldNormalizer->normalizeField('children', new \stdClass(), $this->getNormalizerContext());
    }

    public function testNormalize()
    {
        $parent = $this->getParent();
        $parent->setChildren([
            $this->getChild()->setName('name1'),
            $this->getChild()->setName('name2'),
        ]);

        $fieldNormalizer = new EmbedManyFieldNormalizer($this->getAccessor());

        self::assertSame(
            [['name' => 'name1'], ['name' => 'name2']],
            $fieldNormalizer->normalizeField(
                'children',
                $parent,
                $this->getNormalizerContext(),
                $this->getNormalizer()
            )
        );
    }

    public function testNormalizeEmpty()
    {
        $parent = $this->getParent();
        $parent->setChildren([]);

        $fieldNormalizer = new EmbedManyFieldNormalizer($this->getAccessor());

        self::assertSame(
            [],
            $fieldNormalizer->normalizeField(
                'children',
                $parent,
                $this->getNormalizerContext(),
                $this->getNormalizer()
            )
        );
    }

    public function testNormalizeNull()
    {
        $parent = $this->getParent();

        $fieldNormalizer = new EmbedManyFieldNormalizer($this->getAccessor());

        self::assertNull(
            $fieldNormalizer->normalizeField(
                'children',
                $parent,
                $this->getNormalizerContext(),
                $this->getNormalizer()
            )
        );
    }

    /**
     * @return object
     */
    private function getParent()
    {
        return new class() {
            /**
             * @var array|null
             */
            private $children;

            /**
             * @return array
             */
            public function getChildren()
            {
                return $this->children;
            }

            /**
             * @param array|null $children
             *
             * @return self
             */
            public function setChildren(array $children = null): self
            {
                $this->children = $children;

                return $this;
            }
        };
    }

    /**
     * @return object
     */
    private function getChild()
    {
        return new class() {
            /**
             * @var string
             */
            private $name;

            /**
             * @return string
             */
            public function getName(): string
            {
                return $this->name;
            }

            /**
             * @param string $name
             *
             * @return self
             */
            public function setName(string $name): self
            {
                $this->name = $name;

                return $this;
            }
        };
    }

    /**
     * @return AccessorInterface
     */
    private function getAccessor(): AccessorInterface
    {
        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockBuilder(AccessorInterface::class)->getMockForAbstractClass();

        $accessor->expects(self::any())->method('getValue')->willReturnCallback(function ($object) {
            return $object->getChildren();
        });

        return $accessor;
    }

    /**
     * @return NormalizerContextInterface
     */
    private function getNormalizerContext(): NormalizerContextInterface
    {
        /** @var NormalizerContextInterface|MockObject $context */
        $context = $this->getMockBuilder(NormalizerContextInterface::class)->getMockForAbstractClass();

        return $context;
    }

    /**
     * @return NormalizerInterface
     */
    private function getNormalizer(): NormalizerInterface
    {
        /** @var NormalizerInterface|MockObject $normalizer */
        $normalizer = $this->getMockBuilder(NormalizerInterface::class)->getMockForAbstractClass();

        $normalizer->expects(self::any())->method('normalize')->willReturnCallback(
            function ($object, NormalizerContextInterface $context = null, string $path = '') {
                return ['name' => $object->getName()];
            }
        );

        return $normalizer;
    }
}
