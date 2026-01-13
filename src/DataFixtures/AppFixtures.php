<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;

class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // 1. ADMIN (Pour ne pas perdre votre accès)
        $admin = new User();
        $admin->setEmail('admin@test.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setUsername('Admin');
        $admin->setPhone('0601010101');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'password'));
        $manager->persist($admin);

        // 2. DONNÉES DE PRODUITS
        $shopData = [
            'Informatique' => ['MacBook Pro M2', 'Clavier Gamer', 'Écran 4K Dell', 'Souris Sans Fil', 'Disque SSD 1To'],
            'Téléphonie' => ['iPhone 15', 'Samsung Galaxy S24', 'Chargeur Rapide', 'Coque Protection', 'AirPods Pro'],
            'Mode' => ['Jean Slim', 'T-shirt Coton', 'Veste Cuir', 'Baskets Nike', 'Sac à dos'],
            'Maison' => ['Machine à Café', 'Aspirateur Robot', 'Lampe LED', 'Canapé 3 places', 'Table Basse']
        ];

        foreach ($shopData as $categoryName => $productsNames) {
            $category = new Category();
            $category->setName($categoryName);
            $manager->persist($category);

            foreach ($productsNames as $productName) {
                $product = new Product();
                $product->setTitle($productName);
                $product->setPrice($faker->randomFloat(2, 20, 1500));
                $product->setDescription("Description officielle du produit " . $productName);
                $product->setImage('https://picsum.photos/300/300');
                $product->setCategory($category);

                // STOCK ALÉATOIRE (Entre 0 et 20)
                // Cela créera automatiquement des ruptures de stock (0)
                $product->setStock($faker->numberBetween(0, 20));

                $manager->persist($product);
            }
        }
        $manager->flush();
    }
}
