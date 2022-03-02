<?php

namespace App\Factory;

use App\Entity\Phone;
use App\Repository\PhoneRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Phone>
 *
 * @method static Phone|Proxy createOne(array $attributes = [])
 * @method static Phone[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Phone|Proxy find(object|array|mixed $criteria)
 * @method static Phone|Proxy findOrCreate(array $attributes)
 * @method static Phone|Proxy first(string $sortedField = 'id')
 * @method static Phone|Proxy last(string $sortedField = 'id')
 * @method static Phone|Proxy random(array $attributes = [])
 * @method static Phone|Proxy randomOrCreate(array $attributes = [])
 * @method static Phone[]|Proxy[] all()
 * @method static Phone[]|Proxy[] findBy(array $attributes)
 * @method static Phone[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Phone[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static PhoneRepository|RepositoryProxy repository()
 * @method Phone|Proxy create(array|callable $attributes = [])
 */
final class PhoneFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function getDefaults(): array
    {
        $rams = ['4 Go', '8 Go', '12 Go'];
        $screenSize = self::faker()->numberBetween(4, 9) . ' "';
        $weight = self::faker()->numberBetween(100, 350) . ' grammes';
        $storages = ['64 Go', '128 Go', '250 Go'];
        $cameras = [
            '50 mégapixels; Caméra arrière: 50 MP + 50 MP + 13 MP + 3 MP. Caméra frontale: 32MP',
            'Caméra avant : 11,1 Mpx // Caméra arrière : 50Mpx',
            '48 mégapixels; Triple capteur 48Mp f/1.2 - Ultra grand angle 5Mp f/2.2 - Capteur mode portrait 2Mp f/2.4'
        ];

        return [
            'brand' => self::faker()->word(),
            'model' => self::faker()->word(),
            'description' => self::faker()->text('200'),
            'screenSize' => $screenSize,
            'weight' => $weight,
            'processor' => self::faker()->text(),
            'ram' => self::faker()->randomElement($rams),
            'storage' => self::faker()->randomElement($storages),
            'camera' => self::faker()->randomElement($cameras),
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(Phone $phone): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Phone::class;
    }
}
