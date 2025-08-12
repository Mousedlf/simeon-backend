<?php

namespace App\Controller;

use App\Repository\ActivityCategoryRepository;
use App\Repository\ExpenseCategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/category')]
class CategoryController extends AbstractController
{
    #[Route('/all/expense', methods: ['GET'])]
    public function indexAllExpenseCategories(
        ExpenseCategoryRepository $expenseCategoryRepository,
    ): Response
    {
        // QUAND CREATION CATEGORIES PERSO POSSIBLE
        // les recup grâce a prop ofUser()
        // et les ajouter a celles par default
        // categories = celles ou ofUser est null ainsi que ofUser = currentUser
        // ajouter query dans Repository getUserandDefaultCategories

        return $this->json($expenseCategoryRepository->findAll(), Response::HTTP_OK, [], ['groups' => ['expenseCategory:index']]);
    }

    #[Route('/all/activity', methods: ['GET'])]
    public function indexAllActivityCategories(
        ActivityCategoryRepository $activityCategoryRepository,
    ): Response
    {
        // idem

        return $this->json($activityCategoryRepository->findAll(), Response::HTTP_OK, [], ['groups' => ['activityCategory:index']]);
    }
}
