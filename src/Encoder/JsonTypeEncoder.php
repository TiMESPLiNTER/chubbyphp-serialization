<?php

declare(strict_types=1);

namespace Chubbyphp\Serialization\Encoder;

final class JsonTypeEncoder implements TypeEncoderInterface
{
    /**
     * @var bool
     */
    private $prettyPrint;

    /**
     * @param bool $prettyPrint
     */
    public function __construct(bool $prettyPrint = false)
    {
        $this->prettyPrint = $prettyPrint;
    }

    /**
     * @return string
     */
    public function getContentType(): string
    {
        return 'application/json';
    }

    /**
     * @param array $data
     *
     * @return string
     */
    public function encode(array $data): string
    {
        $options = JSON_PRESERVE_ZERO_FRACTION | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
        if ($this->prettyPrint) {
            $options = $options | JSON_PRETTY_PRINT;
        }

        return json_encode($data, $options);
    }
}
