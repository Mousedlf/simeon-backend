<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', methods: ['POST'])]
    public function register(
        Request                     $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface      $manager,
        UserRepository              $userRepository,
        SerializerInterface         $serializer
    ): Response
    {
        $json = $request->getContent();
        $user = $serializer->deserialize($json, User::class, 'json');

        $user->setPassword(
            $userPasswordHasher->hashPassword(
                $user,
                $user->getPassword()
            )
        );
        $user->setCreatedAt(new \DateTimeImmutable());

        $taken = $userRepository->findOneBy(['username' => $user->getUsername()]);
        if (!$taken) {
            $manager->persist($user);
            $manager->flush();

            $response = [
                'username'
            ];
            return $this->json("user " . $user->getUsername() . " registered", 200);
        } else {
            return $this->json("username already taken", 401);
        }
    }
}
