<?php


namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public const PLACE_IMG = ['technics', 'people', 'nature', 'city', 'animals', 'sports'];

    public const CATEGORIES = ['Technologie', 'People', 'Nature', 'Ville', 'Animaux', 'Sport'];

    public function load(ObjectManager $manager)
    {
        foreach (self::CATEGORIES as $index => $value) {
            $category = new Category();
            $category->setName($value);
            $manager->persist($category);
            $this->addReference('category' . $index, $category);
        }

        $manager->flush();
    }
}