<?php

namespace App\Controller;

use App\Entity\DayOfTrip;
use App\Entity\Expense;
use App\Entity\Trip;
use App\Repository\ExpenseRepository;
use App\Repository\TripParticipantRepository;
use App\Service\ExpenseService;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/expense')]
class ExpenseController extends AbstractController
{
    /**
     * Get all expenses of a trip.
     * @param Trip|null $trip
     * @param TripParticipantRepository $tripParticipantRepository
     * @return Response
     */
    #[Route('/all/trip/{id}', methods: ['GET'])]
    public function indexAllTripExpensesAndGetTotal(
        ?Trip                     $trip,
        TripParticipantRepository $tripParticipantRepository
    ): Response
    {
        if (!$trip) {
            return $this->json("trip not found", Response::HTTP_NOT_FOUND);
        }

        $currentParticipant = $tripParticipantRepository->findOneParticipant($this->getUser(), $trip);
        if (!$currentParticipant) {
            return $this->json("access denied", Response::HTTP_FORBIDDEN);
        }

        $allExpenses = $trip->getExpenses();
        $allCommonExpenses = [];
        $allPersonalExpenses = [];
        $totalCommon = 0;
        $totalPersonal = 0;

        foreach ($allExpenses as $expense) {
            if (!$expense->isPersonal()) {
                $totalCommon += $expense->getSum();
                $allCommonExpenses[] = $expense;
            }
            if ($expense->isPersonal() && $expense->getPaidBy() === $currentParticipant) {
                $totalPersonal += $expense->getSum();
                $allPersonalExpenses[] = $expense;
            }
        }

        $response = [
            "common" => [
                'total' => $totalCommon,
                'expenses' => $allCommonExpenses
            ],
            "personal" => [
                'total' => $totalPersonal,
                'expenses' => $allPersonalExpenses
            ]

        ];

        return $this->json($response, Response::HTTP_OK, [], ['groups' => ['expense:index']]);
    }

    /**
     * Get all common expenses of a specific day of a trip.
     * @param Trip|null $trip
     * @param DayOfTrip|null $day
     * @param TripParticipantRepository $tripParticipantRepository
     * @return Response
     */
    #[Route('/all/trip/{trip}/day/{day}', methods: ['GET'])]
    public function indexAllCommonTripExpensesOfDay(
        #[MapEntity] ?Trip        $trip,
        #[MapEntity] ?DayOfTrip   $day,
        TripParticipantRepository $tripParticipantRepository
    ): Response
    {
        if (!$trip) {
            return $this->json("trip not found", Response::HTTP_NOT_FOUND);
        }
        if (!$day) {
            return $this->json("day not found", Response::HTTP_NOT_FOUND);
        }

        $currentParticipant = $tripParticipantRepository->findOneParticipant($this->getUser(), $trip);
        if (!$currentParticipant) {
            return $this->json("access denied", Response::HTTP_FORBIDDEN);
        }

        //----------------------------------------------ce genre de choses dans service ?
        $allExpenses = $day->getExpenses();
        $allCommonExpenses = [];
        $total = 0;
        foreach ($allExpenses as $expense) {
            if (!$expense->isPersonal()) {
                $allCommonExpenses[] = $expense;
                $total += $expense->getSum();
            }
        }
        $response = [
            'total' => $total,
            'expenses' => $allCommonExpenses
        ];
        //----------------------------------------------

        return $this->json($response, Response::HTTP_OK, [], ['groups' => ['expense:index']]);
    }

    /**
     * Get all common and personal (of the current user) expenses of a specific day of a trip.
     * @param Trip|null $trip
     * @param DayOfTrip|null $day
     * @param TripParticipantRepository $tripParticipantRepository
     * @return Response
     */
    #[Route('/all/trip/{trip}/day/{day}/with-personal', methods: ['GET'])]
    public function indexAllCommonAndPersonalTripExpensesOfDay(
        #[MapEntity] ?Trip        $trip,
        #[MapEntity] ?DayOfTrip   $day,
        TripParticipantRepository $tripParticipantRepository
    ): Response
    {
        if (!$trip) {
            return $this->json("trip not found", Response::HTTP_NOT_FOUND);
        }
        if (!$day) {
            return $this->json("day not found", Response::HTTP_NOT_FOUND);
        }

        $currentParticipant = $tripParticipantRepository->findOneParticipant($this->getUser(), $trip);
        if (!$currentParticipant) {
            return $this->json("access denied", Response::HTTP_FORBIDDEN);
        }

        $allExpenses = $day->getExpenses();
        $allCommonAndPersonalExpenses = [];
        $total = 0;

        foreach ($allExpenses as $expense) {
            if ($expense->getPaidBy() === $currentParticipant) {
                $allCommonAndPersonalExpenses[] = $expense;
            }
        }

        $response = [
            'total' => $total,
            'expenses' => $allCommonAndPersonalExpenses
        ];

        return $this->json($response, Response::HTTP_OK, [], ['groups' => ['expense:index']]);
    }

    /**
     * Get all personal expenses of a trip of the current user.
     * @param Trip|null $trip
     * @param ExpenseRepository $expenseRepository
     * @param TripParticipantRepository $tripParticipantRepository
     * @return Response
     */
    #[Route('/all/trip/{id}/personal', methods: ['GET'])]
    public function indexAllPersonalTripExpenses(
        ?Trip                     $trip,
        ExpenseRepository         $expenseRepository,
        TripParticipantRepository $tripParticipantRepository
    ): Response
    {
        if (!$trip) {
            return $this->json("trip not found", Response::HTTP_NOT_FOUND);
        }

        $currentParticipant = $tripParticipantRepository->findOneParticipant($this->getUser(), $trip);
        if (!$currentParticipant) {
            return $this->json("access denied", Response::HTTP_FORBIDDEN);
        }

        $allExpenses = $currentParticipant->getPaidExpenses();
        $allPersonalExpenses = [];
        $total = 0;

        foreach ($allExpenses as $expense) {
            if ($expense->isPersonal()) {
                $allPersonalExpenses[] = $expense;
                $total += $expense->getSum();
            }
        }

        $response = [
            'total' => $total,
            'expenses' => $allPersonalExpenses
        ];

        return $this->json($response, Response::HTTP_OK, [], ['groups' => ['expense:index']]);
    }

    /**
     * Add expense to a trip.
     * @param Trip|null $trip
     * @param Request $request
     * @param TripParticipantRepository $tripParticipantRepository
     * @param ExpenseService $expenseService
     * @return Response
     */
    #[Route('/new/trip/{id}', methods: ['POST'])]
    public function addExpenseToTrip(?Trip $trip, Request $request, TripParticipantRepository $tripParticipantRepository, ExpenseService $expenseService): Response
    {
        if (!$trip) {
            return $this->json("trip not found", Response::HTTP_NOT_FOUND);
        }
        $participant = $tripParticipantRepository->findOneParticipant($this->getUser(), $trip);
        if (!$participant) {
            return $this->json("not part of this trip", Response::HTTP_FORBIDDEN);
        }

        $res = $expenseService->addExpense($request, $trip);
        return $this->json($res, Response::HTTP_CREATED, [], ['groups' => 'expense:new']);
    }

    /**
     * Delete your personal expenses or common ones.
     * @param Expense|null $expense
     * @param ExpenseService $expenseService
     * @param TripParticipantRepository $tripParticipantRepository
     * @return Response
     */
    #[Route('/{id}/delete', methods: ['DELETE'])]
    public function deleteExpense(
        ?Expense                  $expense,
        ExpenseService            $expenseService,
        TripParticipantRepository $tripParticipantRepository
    ): Response
    {
        if (!$expense) {
            return $this->json("expense not found", Response::HTTP_NOT_FOUND);
        }

        $participant = $tripParticipantRepository->findOneParticipant($this->getUser(), $expense->getTrip());
        if (!$participant) {
            return $this->json("access denied", Response::HTTP_FORBIDDEN);
        }

        switch ($participant) {
            case $expense->isPersonal() && $expense->getPaidBy() === $participant:
            case !$expense->isPersonal() :
                $expenseService->deleteExpense($expense);
                break;
        }

        return $this->json('expense successfully deleted', Response::HTTP_OK);
    }

    /**
     * Edit your personal expenses or common ones.
     * @param Expense|null $expense
     * @param ExpenseService $expenseService
     * @param TripParticipantRepository $tripParticipantRepository
     * @param Request $request
     * @return Response
     */
    #[Route('/{id}/edit', methods: ['PUT'])]
    public function editExpense(
        ?Expense                  $expense,
        ExpenseService            $expenseService,
        TripParticipantRepository $tripParticipantRepository,
        Request                   $request,
    ): Response
    {
        if (!$expense) {
            return $this->json("expense not found", Response::HTTP_NOT_FOUND);
        }

        $participant = $tripParticipantRepository->findOneParticipant($this->getUser(), $expense->getTrip());
        if (!$participant) {
            return $this->json("access denied", Response::HTTP_FORBIDDEN);
        }

        switch ($participant) {
            case $expense->isPersonal() && $expense->getPaidBy() === $participant:
            case !$expense->isPersonal() :
                $res = $expenseService->editExpense($request, $expense);
                break;
            default:
                return $this->json("access denied", Response::HTTP_FORBIDDEN);
        }

        return $this->json($res, Response::HTTP_OK, [], ['groups' => ['expense:new']]);
    }

    /**
     * Set personal budget | updates global budget.
     * @param Trip|null $trip
     * @param TripParticipantRepository $tripParticipantRepository
     * @param ExpenseService $expenseService
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    #[Route('/trip/{trip}/budget', methods: ['PUT'])]
    public function setPersonalBudget(
        ?Trip                     $trip,
        TripParticipantRepository $tripParticipantRepository,
        ExpenseService            $expenseService,
        Request                   $request,
    )
    {
        if (!$trip) {
            return $this->json("trip not found", Response::HTTP_NOT_FOUND);
        }
        $participant = $tripParticipantRepository->findOneParticipant($this->getUser(), $trip);
        if (!$participant) {
            return $this->json("not part of this trip", Response::HTTP_FORBIDDEN);
        }

        return $this->json($expenseService->setPersonalBudget($request, $participant), Response::HTTP_OK, [], ['groups' => ['participant:read']]);
    }


}
