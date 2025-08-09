<?php

namespace App\Controller;

use App\Entity\Currency;
use App\Form\CurrencyType;
use App\Repository\CurrencyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/currency')]
final class CurrencyController extends AbstractController
{

    #[Route('/all', methods: ['GET'])]
    public function getAllCurrencies(CurrencyRepository $currencyRepository): Response
    {
        return $this->json($currencyRepository->findAll(), Response::HTTP_OK, [], ['groups' => 'currency:index']);

    }


}
