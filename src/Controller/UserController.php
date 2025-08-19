<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\TripParticipantRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/user')]
class UserController extends AbstractController
{
    #[Route('/all/public', methods: ['GET'])]
    public function getAllPublicUsers(UserRepository $userRepository): Response
    {
        $publicUsers = $userRepository->findByStatus(true);
        return $this->json($publicUsers, Response::HTTP_OK, [], ['groups' => ['users:index']]);
    }

    /**
     * Get current user informations.
     * @return Response (User)
     */
    #[Route('/profile', methods: ['GET'])]
    public function indexUserInfo(): Response
    {
        return $this->json($this->getUser(), 200, [], ['groups' => 'user:read']);
    }

    /**
     * Change user visibility (public or private).
     * @param User $user
     * @param EntityManagerInterface $manager
     * @return Response (User)
     */
    #[Route('/{id}/edit-visibility', methods: ['GET'])]
    public function editUserVisibility(User $user, EntityManagerInterface $manager): Response
    {
        $currentUser = $this->getUser();
        if ($currentUser !== $user) {
            return $this->json("access denied", 403);
        }

        if ($currentUser->isPublic()) {
            $currentUser->setPublic(false);
        } else {
            $currentUser->setPublic(true);
        }
        $manager->persist($currentUser);
        $manager->flush();

        return $this->json($this->getUser(), 200, [], ['groups' => 'user:read']);
    }

    /**
     * Change user info: username and visibility.
     * @param User $user
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @param UserRepository $userRepository
     * @return Response (string)
     */
    #[Route('/{id}/edit', methods: ['PUT'])]
    public function editUserUsername(
        User $user,
        EntityManagerInterface $manager,
        Request $request,
        UserRepository $userRepository
    ): Response
    {
        $currentUser = $this->getUser();
        if ($currentUser !== $user) {
            return $this->json("access denied", 403);
        }

        $content = $request->getContent();
        $data = json_decode($content, true);
        $newUsername = $data["username"];

        $user->setPublic($data["public"]);

        $taken = $userRepository->findOneBy(['username' => $newUsername]);
        if (!$taken) {
            $user->setUsername($newUsername);
        } else {
            return $this->json("username already taken", 401);
        }

        $manager->persist($user);
        $manager->flush();
        return $this->json($this->getUser(), 200, [], ['groups' => 'user:read']);
    }

    /**
     * Get travel statistics of user
     * @param User|null $user
     * @param TripParticipantRepository $tripParticipantRepository
     * @return Response
     */
    #[Route('/{id}/stats', methods: ['GET'])]
    public function getTravelStats(
        ?User $user,
        TripParticipantRepository $tripParticipantRepository,
    ): Response
    {
        if(!$user){
            return $this->json("user not found", 404);
        }

        $userParticipatesIn = $tripParticipantRepository->findByUser($user);

        $totalDayCount = 0;
        foreach ($userParticipatesIn as $participant) {
            $trip = $participant->getTrip();
            $totalDayCount += count($trip->getDaysOfTrip());
        }

        $response= [
            'totalDayCount' => $totalDayCount,
            'totalTripCount' => count($userParticipatesIn)
        ];

        return $this->json($response, Response::HTTP_OK);

    }
}
