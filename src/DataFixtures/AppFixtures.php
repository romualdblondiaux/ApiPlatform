<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Invoice;
use App\Entity\Customer;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr-FR');

        // gestion de l'admin
        $adminUser = new User();
        $adminUser->setFirstName('Romuald')
                ->setLastName('Blondiaux')
                ->setEmail('romuald.blondiaux@gmail.com')
                ->setPassword($this->passwordEncoder->encodePassword($adminUser,'admin'))
                ->setRoles(['ROLE_ADMIN']);
        $manager->persist($adminUser);

        // gestion des users
        for($u=0 ; $u<10 ; $u++)
        {
            $chrono = 1;
            $user = new User();
            $user->setFirstName($faker->FirstName())
                ->setLastName($faker->LastName())
                ->setEmail($faker->email())
                ->setPassword($this->passwordEncoder->encodePassword($adminUser,'password'));
            $manager->persist($user);

            // gestion des clients
            for($c=0 ; $c < rand(5,20)  ; $c++)
            {
                $customer = new Customer();
                $customer->setFirstName($faker->FirstName())
                    ->setLastName($faker->LastName())
                    ->setCompany($faker->company())
                    ->setEmail($faker->email())
                    ->setUser($user);
                $manager->persist($customer);

                // gestion des factures
                for($i=0 ; $i < rand(3,10)  ; $i++)
                {
                    $invoice = new Invoice();
                    $invoice->setAmount($faker->randomFloat(2,250,5000))
                            ->setSentAt($faker->dateTimeBetween('-6 months'))
                            ->setStatus($faker->randomElement(['SENT','CANCELLED','PAID']))
                            ->setCustomer($customer)
                            ->setChrono($chrono);
                    $chrono++;
                    $manager->persist($invoice);

                }
            }


        }
        
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
