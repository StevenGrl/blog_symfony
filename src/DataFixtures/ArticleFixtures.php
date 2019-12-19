<?php


namespace App\DataFixtures;

use App\Entity\Article;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class ArticleFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $categories = CategoryFixtures::CATEGORIES;
        $users = ['axel', 'steven'];
        $faker = Factory::create('fr_FR');
        foreach (range(1,50) as $index) {
            $randomUser = $users[$faker->numberBetween(0, 1)];
            $article = new Article();
            $article->setTitle($faker->words($faker->numberBetween(1, 4), $asText = true));
            $article->setAuthor($faker->name);
            $article->setContent($faker->text(700));
            $article->setPublished($faker->boolean);
            $article->setNbViews($faker->numberBetween(10, 500));
            $category =  $this->getReference('category' . $faker->numberBetween(0, count($categories) - 1));
            $random = $faker->numberBetween(1, 10);
            $article->addCategory($category);
            $article->setImage('images/' . $category . '/' . $random . '.jpg');
            $article->setThumbnail('images/' . $category . '/' . $random . '.jpg');
            $article->setUser($this->getReference($randomUser));
            $this->addReference('article' . $index, $article);
            if ($faker->boolean) {
                $user = $this->getReference($randomUser == 'steven ' ? $users[0] : $users[1]);
                $user->addFavouriteArticle($article);
                $manager->persist($user);
            }
            $manager->persist($article);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            CategoryFixtures::class,
            UserFixtures::class,
        );
    }
}