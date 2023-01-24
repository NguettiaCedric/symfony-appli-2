<?php

namespace App\DataFixtures;

use App\Entity\Profile;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProfileFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $profile = new Profile();
        $profile->setRs('Facebook');
        $profile->setUrl('https://mmonfacebook.com');

        $profile1 = new Profile();
        $profile1->setRs('twitter');
        $profile1->setUrl('https://mmontwitter.com');

        $profile2 = new Profile();
        $profile2->setRs('Linkend');
        $profile2->setUrl('https://monlinkend.com');

        $profile3 = new Profile();
        $profile3->setRs('Github');
        $profile3->setUrl('https://mmongithub.com');

        $manager->persist($profile);
        $manager->persist($profile1);
        $manager->persist($profile2);
        $manager->persist($profile3);

        $manager->flush();
    }
}
