<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Serialization\Transformer;

use Chubbyphp\Serialization\Transformer\JsonTransformer;

/**
 * @covers \Chubbyphp\Serialization\Transformer\JsonTransformer
 */
class JsonTransformerTest extends AbstractTransformerTest
{
    /**
     * @dataProvider dataProvider
     *
     * @param array $data
     */
    public function testFormat(array $data)
    {
        $jsonTransformer = new JsonTransformer(JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

        $json = $jsonTransformer->transform($data);

        $expectedJson = <<<EOD
{
    "page": 1,
    "per_page": 10,
    "search": null,
    "sort": "name",
    "order": "asc",
    "_embedded": {
        "items": [
            {
                "id": "id1",
                "name": "A fancy Name",
                "progress": 76.8,
                "active": true,
                "_links": {
                    "read": {
                        "href": "http://test.com/items/id1",
                        "method": "GET"
                    },
                    "update": {
                        "href": "http://test.com/items/id1",
                        "method": "PUT"
                    },
                    "delete": {
                        "href": "http://test.com/items/id1",
                        "method": "DELETE"
                    }
                }
            },
            {
                "id": "id2",
                "name": "B fancy Name",
                "progress": 24.7,
                "active": true,
                "_links": {
                    "read": {
                        "href": "http://test.com/items/id2",
                        "method": "GET"
                    },
                    "update": {
                        "href": "http://test.com/items/id2",
                        "method": "PUT"
                    },
                    "delete": {
                        "href": "http://test.com/items/id2",
                        "method": "DELETE"
                    }
                }
            },
            {
                "id": "id3",
                "name": "C fancy Name",
                "progress": 100,
                "active": false,
                "_links": {
                    "read": {
                        "href": "http://test.com/items/id3",
                        "method": "GET"
                    },
                    "update": {
                        "href": "http://test.com/items/id3",
                        "method": "PUT"
                    },
                    "delete": {
                        "href": "http://test.com/items/id3",
                        "method": "DELETE"
                    }
                }
            }
        ]
    },
    "_links": {
        "self": {
            "href": "http://test.com/items/?page=1&per_page=10&sort=name&order=asc",
            "method": "GET"
        },
        "create": {
            "href": "http://test.com/items/",
            "method": "POST"
        }
    }
}
EOD;
        self::assertEquals($expectedJson, $json);
    }
}