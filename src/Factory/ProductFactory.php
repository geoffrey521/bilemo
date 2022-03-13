<?php

namespace App\Factory;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Product>
 *
 * @method static Product|Proxy createOne(array $attributes = [])
 * @method static Product[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Product|Proxy find(object|array|mixed $criteria)
 * @method static Product|Proxy findOrCreate(array $attributes)
 * @method static Product|Proxy first(string $sortedField = 'id')
 * @method static Product|Proxy last(string $sortedField = 'id')
 * @method static Product|Proxy random(array $attributes = [])
 * @method static Product|Proxy randomOrCreate(array $attributes = [])
 * @method static Product[]|Proxy[] all()
 * @method static Product[]|Proxy[] findBy(array $attributes)
 * @method static Product[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Product[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static ProductRepository|RepositoryProxy repository()
 * @method Product|Proxy create(array|callable $attributes = [])
 */
final class ProductFactory extends ModelFactory
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
        return Product::class;
    }
}
