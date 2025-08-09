<?php

namespace App\Command;

use App\Repository\CurrencyRepository;
use App\Service\CurrencyConverter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:update-exchange-rates',
    description: 'Fetches exchange rates for all currencies, with the euro as base currency.',
)]
class UpdateExchangeRatesCommand extends Command
{
    public function __construct(
        private CurrencyRepository $currencyRepository,
        private CurrencyConverter $currencyConverter,
        private EntityManagerInterface $entityManager,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Mise à jour des taux de change');

        $baseCurrencyCode = 'EUR';

        try {
            $exchangeRates = $this->currencyConverter->getAllExchangeRates($baseCurrencyCode);
        } catch (\Exception $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }

        $currencies = $this->currencyRepository->findAll();
        $totalUpdated = 0;

        foreach ($currencies as $currency) {
            $currencyCode = $currency->getCode();

            if ($currencyCode === $baseCurrencyCode) {
                $currency->setExchangeRate(1.0);
            } elseif (isset($exchangeRates[$currencyCode])) {
                $rate = $exchangeRates[$currencyCode]['value'];
                $currency->setExchangeRate($rate);
                $totalUpdated++;
                $io->text("Taux de change mis à jour : 1 {$baseCurrencyCode} = {$rate} {$currencyCode}");
            } else {
                $io->warning("Aucun taux de change n'a été trouvé pour la devise : {$currencyCode}");
            }
        }

        $this->entityManager->flush();
        $io->success("Opération terminée. {$totalUpdated} taux de change ont été mis à jour.");

        return Command::SUCCESS;
    }
}

