<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Product;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\AsciiSlugger;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $category = new Category(); // crée la nouvelle catégorie
        $category->setName('Ambiance'); // définit le nom de la catégorie
        $category->setSlug('ambiance'); // définit le slug de la catégorie
        $manager->persist($category); // précise au gestionnaire qu'on va vouloir envoyer un obket en base de données (le rend persistant / liste d'attente)

        $category = new Category();
        $category->setName('Stratégie');
        $category->setSlug('strategie');
        $manager->persist($category);

        $category = new Category();
        $category->setName('Junior');
        $category->setSlug('junior');
        $manager->persist($category);

        $category = new Category();
        $category->setName('Classique');
        $category->setSlug('classique');
        $manager->persist($category);

        $manager->flush(); // ce flush est nécessaire pour envoyer les catégories en base de données car on en aura besoin juste après pour alimenter les produits

        $faker = Factory::create();

        $categories = $manager->getRepository(Category::class)->findAll(); // récupère les catégories en base de données

        $slugger = new AsciiSlugger();

        for ($i = 1; $i <= 20; $i++) {
            $product = new Product();
            $product->setName($faker->text(35));
            $product->setSlug(strtolower($slugger->slug($product->getName())));
            $product->setAbstract($faker->text(255));
            $product->setDescription($faker->text(1000));
            $product->setQuantity($faker->numberBetween(0, 100));
            $product->setPrice($faker->randomFloat(2, 4, 200));
            $product->setMinPlayers($faker->numberBetween(1,3));
            $product->setMaxPlayers($faker->numberBetween(3, 10));
            $product->setMinimumAge($faker->numberBetween(3, 18));
            $product->setDuration(new \DateTime());
            $product->setEditor($faker->firstName());
            $product->setTheme($faker->word());
            $product->setMecanism($faker->word());
            $product->setCreatedAt(new \DateTimeImmutable());

            $index = array_rand($categories, 1); // renvoit un index aléatoire du tableau contenant les catégories
            $category = $categories[$index]; // récupère la valeur liée à cet index
            $product->setCategory($category); // définit la catégorie récupérer à la ligne précédente

            $manager->persist($product);
        }

        $manager->flush(); // envoit les objets persistés en base de données
    }
}
