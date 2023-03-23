<?php

declare(strict_types=1);

namespace PsrDiscovery\Implementations\Psr17;

use Psr\Http\Message\ResponseFactoryInterface;
use PsrDiscovery\Collections\CandidatesCollection;
use PsrDiscovery\Contracts\Implementations\Psr17\ResponseFactoriesContract;
use PsrDiscovery\Discover;
use PsrDiscovery\Entities\CandidateEntity;
use PsrDiscovery\Implementations\Implementation;

final class ResponseFactories extends Implementation implements ResponseFactoriesContract
{
    private static ?CandidatesCollection $candidates                     = null;
    private static ?ResponseFactoryInterface $singleton                  = null;
    private static ?ResponseFactoryInterface $using                      = null;

    public static function add(CandidateEntity $candidate): void
    {
        parent::add($candidate);
        self::use(null);
    }

    /**
     * @psalm-suppress MixedInferredReturnType,MixedReturnStatement
     */
    public static function candidates(): CandidatesCollection
    {
        if (null !== self::$candidates) {
            return self::$candidates;
        }

        self::$candidates = CandidatesCollection::create();

        // psr-mock/http-factory-implementation 1.0+ is PSR-18 compatible.
        self::$candidates->add(CandidateEntity::create(
            package: 'psr-mock/http-factory-implementation',
            version: '^1.0',
            builder: static fn (string $class = '\PsrMock\Psr17\ResponseFactory'): object => new $class(),
        ));

        // nyholm/psr7 1.2+ is PSR-17 compatible.
        self::$candidates->add(CandidateEntity::create(
            package: 'nyholm/psr7',
            version: '^1.2',
            builder: static fn (string $class = '\Nyholm\Psr7\Factory\Psr17Factory'): object => new $class(),
        ));

        // guzzlehttp/psr7 1.6+ is PSR-17 compatible.
        self::$candidates->add(CandidateEntity::create(
            package: 'guzzlehttp/psr7',
            version: '^1.6',
            builder: static fn (string $class = '\GuzzleHttp\Psr7\HttpFactory'): object => new $class(),
        ));

        // zendframework/zend-diactoros 2.0+ is PSR-17 compatible. (Caution: Abandoned!)
        self::$candidates->add(CandidateEntity::create(
            package: 'zendframework/zend-diactoros',
            version: '^2.0',
            builder: static fn (string $class = '\Zend\Diactoros\ResponseFactory'): object => new $class(),
        ));

        // http-interop/http-factory-guzzle 1.0+ is PSR-17 compatible.
        self::$candidates->add(CandidateEntity::create(
            package: 'http-interop/http-factory-guzzle',
            version: '^1.0',
            builder: static fn (string $class = '\Http\Factory\Guzzle\ResponseFactory'): object => new $class(),
        ));

        // laminas/laminas-diactoros 2.0+ is PSR-17 compatible
        self::$candidates->add(CandidateEntity::create(
            package: 'laminas/laminas-diactoros',
            version: '^2.0',
            builder: static fn (string $class = '\Laminas\Diactoros\ResponseFactory'): object => new $class(),
        ));

        // slim/psr7 1.0+ is PSR-17 compatible.
        self::$candidates->add(CandidateEntity::create(
            package: 'slim/psr7',
            version: '^1.0',
            builder: static fn (string $class = '\Slim\Psr7\Factory\ResponseFactory'): object => new $class(),
        ));

        // typo3/core 10.0+ is PSR-17 compatible.
        self::$candidates->add(CandidateEntity::create(
            package: 'typo3/core',
            version: '^10.0',
            builder: static fn (string $class = '\TYPO3\CMS\Core\Http\ResponseFactory'): object => new $class(),
        ));

        // nimbly/capsule 1.0+ is PSR-17 compatible.
        self::$candidates->add(CandidateEntity::create(
            package: 'nimbly/capsule',
            version: '^1.0',
            builder: static fn (string $class = '\Nimbly\Capsule\Factory\ResponseFactory'): object => new $class(),
        ));

        // httpsoft/http-message 1.0+ is PSR-17 compatible.
        self::$candidates->add(CandidateEntity::create(
            package: 'httpsoft/http-message',
            version: '^1.0',
            builder: static fn (string $class = '\HttpSoft\Message\ResponseFactory'): object => new $class(),
        ));

        return self::$candidates;
    }

    /**
     * @psalm-suppress MoreSpecificReturnType,LessSpecificReturnStatement
     */
    public static function discover(): ?ResponseFactoryInterface
    {
        if (null !== self::$using) {
            return self::$using;
        }

        return Discover::httpResponseFactory();
    }

    public static function prefer(string $package): void
    {
        self::$candidates ??= CandidatesCollection::create();
        parent::prefer($package);
        self::use(null);
    }

    public static function set(CandidatesCollection $candidates): void
    {
        self::$candidates ??= CandidatesCollection::create();
        parent::set($candidates);
        self::use(null);
    }

    public static function singleton(): ?ResponseFactoryInterface
    {
        if (null !== self::$using) {
            return self::$using;
        }

        return self::$singleton ??= self::discover();
    }

    public static function use(?ResponseFactoryInterface $instance): void
    {
        self::$singleton = $instance;
        self::$using     = $instance;
    }
}
