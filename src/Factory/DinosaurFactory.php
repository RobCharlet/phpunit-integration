<?php

namespace App\Factory;

use App\Entity\Dinosaur;
use App\Enum\HealthStatus;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Dinosaur>
 */
final class DinosaurFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    public static function class(): string
    {
        return Dinosaur::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'enclosure' => self::faker()->text(),
            'genus' => self::faker()->text(),
            'health' => self::faker()->randomElement(HealthStatus::cases()),
            'length' => self::faker()->randomNumber(),
            'name' => self::faker()->text(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Dinosaur $dinosaur): void {})
        ;
    }
}
