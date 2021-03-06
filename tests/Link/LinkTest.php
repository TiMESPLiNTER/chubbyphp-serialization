<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Serialization\Link;

use Chubbyphp\Serialization\Link\Link;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Serialization\Link\Link
 */
class LinkTest extends TestCase
{
    public function testLink()
    {
        $link = new Link('/api/models', ['models'], ['method' => 'GET']);

        self::assertSame('/api/models', $link->getHref());
        self::assertFalse($link->isTemplated());
        self::assertSame(['models'], $link->getRels());
        self::assertSame(['method' => 'GET'], $link->getAttributes());
    }

    public function testTemplatedLink()
    {
        $link = new Link('/api/models/{id}', [], []);

        self::assertSame('/api/models/{id}', $link->getHref());
        self::assertTrue($link->isTemplated());
        self::assertSame([], $link->getRels());
        self::assertSame([], $link->getAttributes());
    }
}
