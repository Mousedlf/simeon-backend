<?php

namespace App\Service;

use App\Entity\Expense;
use App\Entity\Trip;
use App\Repository\DayOfTripRepository;
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
        public DayOfTripRepository $dayOfTripRepository,
    )
    {
    }


    /**
     * Add expense.
     * @param Request $request
     * @param Trip $trip
     * @return Expense|string
     */
    public function addExpense(Request $request, Trip $trip): Expense|string
    {
        $expense = new Expense();
        $data = $request->toArray();

        $expense->setTrip($trip);
        $expense->setDayOfTrip($this->dayOfTripRepository->findOneBy(['id'=>$data['dayOfTrip']]));
        $expense->setPaidBy($this->tripParticipantRepository->findOneParticipant($data['paidBy'], $trip));
        $expense->setName($data['name']);
        $expense->setSum($data['sum']);
        $expense->setPaymentMethod($data['paymentMethod']);
        $expense->setPersonal($data['personal']);
        $expense->setDivide($data['divide']);

        if ($data['divide']) {
            foreach ($data['divideBetween'] as $id){
                $participant = $this->tripParticipantRepository->findOneParticipant($id, $trip);
                if($participant && $participant !== $expense->getPaidBy()){
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
     * Delete expense.
     * @param Expense $expense
     * @return string
     */
    public function deleteExpense(Expense $expense): string
    {
        $this->manager->remove($expense);
        $this->manager->flush();

        return "expense successfully deleted";
    }
}