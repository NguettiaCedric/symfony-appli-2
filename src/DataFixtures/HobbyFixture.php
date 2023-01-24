<?php

namespace App\DataFixtures;

use App\Entity\Hobby;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class HobbyFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $datas =  [
            "Yoga",
            "Foot",
            "Cuisine",
            "Blopping",
            "Lecture",
            "Apprendre une langue",
            "Dessin",
            "photographie",
            "coloriage",
            "peintre",
            "Ce lancer dans le tissage de tapis",
            "Créer des vêtements ou des cosplay",
            "Jouer aux fléchettes",
            "Apprendre à chanter",
        ];

        for ($i=0; $i < count($datas); $i++) { 
            $hobby = new Hobby();
            $hobby->setDesignation($datas[$i]);
            $manager->persist($hobby);
        }

        $manager->flush();
    }
}
