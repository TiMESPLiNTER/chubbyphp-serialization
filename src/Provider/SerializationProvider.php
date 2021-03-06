<?php

declare(strict_types=1);

namespace Chubbyphp\Serialization\Provider;

use Chubbyphp\Serialization\Encoder\Encoder;
use Chubbyphp\Serialization\Encoder\JsonTypeEncoder;
use Chubbyphp\Serialization\Encoder\JsonxTypeEncoder;
use Chubbyphp\Serialization\Encoder\UrlEncodedTypeEncoder;
use Chubbyphp\Serialization\Encoder\XmlTypeEncoder;
use Chubbyphp\Serialization\Encoder\YamlTypeEncoder;
use Chubbyphp\Serialization\Normalizer\Normalizer;
use Chubbyphp\Serialization\Normalizer\NormalizerObjectMappingRegistry;
use Chubbyphp\Serialization\Serializer;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\Yaml\Yaml;

final class SerializationProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container)
    {
        $container['serializer'] = function () use ($container) {
            return new Serializer($container['serializer.normalizer'], $container['serializer.encoder']);
        };

        $container['serializer.normalizer'] = function () use ($container) {
            return new Normalizer(
                $container['serializer.normalizer.objectmappingregistry'],
                $container['logger'] ?? null
            );
        };

        $container['serializer.normalizer.objectmappingregistry'] = function () use ($container) {
            return new NormalizerObjectMappingRegistry($container['serializer.normalizer.objectmappings']);
        };

        $container['serializer.normalizer.objectmappings'] = function () {
            return [];
        };

        $container['serializer.encoder'] = function () use ($container) {
            return new Encoder($container['serializer.encodertypes']);
        };

        $container['serializer.encodertypes'] = function () {
            $encoderTypes = [];

            $encoderTypes[] = new JsonTypeEncoder();
            $encoderTypes[] = new JsonxTypeEncoder();
            $encoderTypes[] = new UrlEncodedTypeEncoder();
            $encoderTypes[] = new XmlTypeEncoder();

            if (class_exists(Yaml::class)) {
                $encoderTypes[] = new YamlTypeEncoder();
            }

            return $encoderTypes;
        };
    }
}
