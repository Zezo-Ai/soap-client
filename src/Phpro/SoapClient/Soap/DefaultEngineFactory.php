<?php
declare(strict_types=1);

namespace Phpro\SoapClient\Soap;

use Psr\Cache\CacheItemPoolInterface;
use Soap\CachedEngine\CachedDriver;
use Soap\Encoding\Driver;
use Soap\Engine\Engine;
use Soap\Engine\LazyEngine;
use Soap\Engine\SimpleEngine;
use Soap\WsdlReader\Wsdl1Reader;

final class DefaultEngineFactory
{
    public static function create(
        EngineOptions $options
    ): Engine {
        return new LazyEngine(static fn (): Engine => self::configureEngine($options));
    }

    private static function configureEngine(EngineOptions $options): Engine
    {
        $driver = $options->getCache()->mapOrElse(
            static fn (CacheItemPoolInterface $cache) => new CachedDriver(
                $cache,
                $options->getCacheConfig(),
                static fn () => self::configureDriver($options),
            ),
            static fn () => self::configureDriver($options)
        )->unwrap();

        return new SimpleEngine(
            $driver,
            $options->getTransport()
        );
    }

    private static function configureDriver(EngineOptions $options): Driver
    {
        $wsdl = (new Wsdl1Reader($options->getWsdlLoader()))(
            $options->getWsdl(),
            $options->getWsdlParserContext()
        );

        return Driver::createFromWsdl1(
            $wsdl,
            $options->getWsdlServiceSelectionCriteria(),
            $options->getEncoderRegistry()
        );
    }
}
