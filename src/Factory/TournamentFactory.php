<?php

namespace App\Factory;

use App\Entity\Tournament;
use App\Repository\TournamentRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Tournament>
 *
 * @method static Tournament|Proxy createOne(array $attributes = [])
 * @method static Tournament[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Tournament|Proxy find(object|array|mixed $criteria)
 * @method static Tournament|Proxy findOrCreate(array $attributes)
 * @method static Tournament|Proxy first(string $sortedField = 'id')
 * @method static Tournament|Proxy last(string $sortedField = 'id')
 * @method static Tournament|Proxy random(array $attributes = [])
 * @method static Tournament|Proxy randomOrCreate(array $attributes = [])
 * @method static Tournament[]|Proxy[] all()
 * @method static Tournament[]|Proxy[] findBy(array $attributes)
 * @method static Tournament[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Tournament[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static TournamentRepository|RepositoryProxy repository()
 * @method Tournament|Proxy create(array|callable $attributes = [])
 */
final class TournamentFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();

        // TODO inject services if required (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services)
    }

    protected function getDefaults(): array
    {
        return [
            // TODO add your default values here (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories)
            'name' => self::faker()->firstNameFemale(),
            'startedAt' => self::faker()->dateTimeBetween('now', '+1 years'),
            'categories' => self::faker()->randomElement([['Junior'], ['Senior'], ['Veteran']]),
            'gender' => self::faker()->randomElement([['f'], ['m']]),
            'maxPlayer' => 50,
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(Tournament $tournament): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Tournament::class;
    }
}
