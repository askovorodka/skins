<?php

namespace AppBundle\Command;

use AppBundle\DTO\InventoryItem;
use AppBundle\Entity\Deposit;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AppCheckFailsItemsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:check_fails_items')
            ->setDescription('find deposits status=completed and value=0 and recount items and value');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $depositService = $this->getContainer()->get('app.deposit_service');
        $logService = $this->getContainer()->get('logger');
        $em = $this->getContainer()->get('doctrine')->getManager();
        $itemPriceService = $this->getContainer()->get('app.items_price_service');
        $depositsList = $depositService->findBy(['status' => Deposit::STATUS_COMPLETED, 'value' => 0]);

        if (!empty($depositsList)) {
            /** @var Deposit $deposit */
            foreach ($depositsList as $deposit) {
                $items = $deposit->getItems();
                if (!empty($items))
                {
                    foreach ($items as $key => $item) {
                        $items[$key] = new InventoryItem($item);
                    }

                    $depositValue = $itemPriceService->calculateInventoryValue($deposit, $items);
                    $itemPriceService->calculateDepositNoTaxValue($deposit, $items);

                    $message =
                        'app:check_fails_items deposit: ' . $deposit->getId() .
                        ',trade_hash:' . $deposit->getTradeHash() .
                        ',currency' . $deposit->getCurrency() .
                        ',value: ' . $depositValue .
                        ',noTaxValue: ' . $deposit->getNoTaxValue() . PHP_EOL;

                    $logService->info($message);

                    $deposit
                        ->setValue($depositValue)
                        ->setItems($items)
                        ->setPushStatus(null);
                    $em->merge($deposit);
                }
            }
            $em->flush();
            $output->writeln('ok');
        }
    }
}
