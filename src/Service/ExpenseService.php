<?php

namespace App\Service;

use App\Entity\Expense;
use App\Entity\Trip;
use App\Entity\TripParticipant;
use App\Repository\CurrencyRepository;
use App\Repository\DayOfTripRepository;
use App\Repository\ExpenseCategoryRepository;
use App\Repository\TripParticipantRepository;
use App\Repository\TripRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class ExpenseService
{
    public function __construct(
        public EntityManagerInterface    $manager,
        public TripRepository            $tripRepository,
        public SerializerInterface       $serializer,
        public TripParticipantRepository $tripParticipantRepository,
        public DayOfTripRepository       $dayOfTripRepository,
        public ExpenseCategoryRepository $expenseCategoryRepository,
        public CurrencyRepository        $currencyRepository,
    )
    {
    }


    /**
     * Add an expense.
     * @param Request $request
     * @param Trip $trip
     * @return Expense|string
     */
    public function addExpense(Request $request, Trip $trip): Expense|string
    {
        $expense = new Expense();
        $data = $request->toArray();

        $expense->setTrip($trip);
        $expense->setDayOfTrip($this->dayOfTripRepository->findOneBy(['id' => $data['dayOfTrip']]));
        $expense->setPaidBy($this->tripParticipantRepository->findOneParticipant($data['paidBy'], $trip));
        $expense->setName($data['name']);
        $expense->setAmountLocalCurrency($data['amountLocalCurrency']);
        $expense->setCurrency($this->currencyRepository->findOneBy(['id' => $data['currency']]));

        if ($expense->getCurrency()->getCode() === 'EUR') {
            $expense->setAmountEuro($data['amountLocalCurrency']);
            $expense->setExchangeRate(1.0);
        } else {
            $exchangeRate = $expense->getCurrency()->getExchangeRate();
            $expense->setExchangeRate($exchangeRate);
            $amountEuro = floatval(number_format($data['amountLocalCurrency'] * $exchangeRate, 2, '.', ' '));
            $expense->setAmountEuro($amountEuro);
        }

        $expense->setPaymentMethod($data['paymentMethod']);
        $expense->setPersonal($data['personal']);
        $expense->setDivide($data['divide']);
        $expense->setCategory($this->expenseCategoryRepository->findOneBy(['id' => $data['category']]));

        if ($data['divide']) {
            foreach ($data['divideBetween'] as $id) {
                $participant = $this->tripParticipantRepository->findOneParticipant($id, $trip);
                if ($participant && $participant !== $expense->getPaidBy()) {
                    $expense->addDivideBetween($participant);
                } else {
                    return "participant " . $id . " not found to divide with OR paid full expense";
                }
            }
        }

        $this->manager->persist($expense);
        $this->manager->flush();

        return $expense;
    }

    /**
     * Delete an expense.
     * @param Expense $expense
     * @return string
     */
    public function deleteExpense(Expense $expense): string
    {
        $this->manager->remove($expense);
        $this->manager->flush();

        return "expense successfully deleted";
    }

    /**
     * Edit an expense.
     * @param Request $request
     * @param Expense $expense
     * @return Expense|string
     */
    public function editExpense(Request $request, Expense $expense): Expense|string
    {
        $data = $request->toArray();

        $expense->setDayOfTrip($this->dayOfTripRepository->findOneBy(['id' => $data['dayOfTrip']]));
        $expense->setPaidBy($this->tripParticipantRepository->findOneParticipant($data['paidBy'], $expense->getTrip()));
        $expense->setName($data['name']);
        $expense->setSum($data['sum']);
        $expense->setPaymentMethod($data['paymentMethod']);
        $expense->setPersonal($data['personal']);
        $expense->setDivide($data['divide']);

        if ($data['divide']) {
            foreach ($data['divideBetween'] as $id) {
                $participant = $this->tripParticipantRepository->findOneParticipant($id, $expense->getTrip());
                if ($participant && $participant !== $expense->getPaidBy()) {
                    $expense->addDivideBetween($participant);
                } else {
                    return "participant " . $id . " not found to divide with OR paid full expense";
                }
            }
        }

        $this->manager->persist($expense);
        $this->manager->flush();

        return $expense;
    }

    /**
     * Set personal budget of a trip participant.
     * @param Request $request
     * @param TripParticipant $participant
     * @return TripParticipant
     */
    public function setPersonalBudget(Request $request, TripParticipant $participant): TripParticipant
    {
        $data = $request->toArray();

        $participant->setBudget($data['budget']);

        $this->manager->persist($participant);
        $this->manager->flush();

        $this->updateGlobalBudget($participant->getTrip());

        return $participant;
    }

    /**
     * Update global budget when a personal budget of a trip participant is set.
     * @param Trip $trip
     * @return void
     */
    public function updateGlobalBudget(Trip $trip): void
    {
        $participants = $trip->getParticipants();
        $budget = 0;

        foreach ($participants as $participant) {
            $budget += $participant->getBudget();
        }

        $trip->setBudget($budget);

        $this->manager->persist($trip);
        $this->manager->flush();
    }
}