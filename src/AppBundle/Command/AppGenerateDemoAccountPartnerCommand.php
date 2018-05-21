<?php

namespace AppBundle\Command;

use AppBundle\Entity\Deposit;
use AppBundle\Entity\Integration;
use AppBundle\Entity\User;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class AppGenerateDemoAccountPartnerCommand.
 */
class AppGenerateDemoAccountPartnerCommand extends ContainerAwareCommand
{
    const PUSHBACK_URL = 'https://example.com/skins4real/push';
    const SUCCESS_URL = 'https://example.com/skins4real/success';
    const FAKE_PRIVATE_KEY = 'SweetLemonade';
    const FAKE_PUBLIC_KEY = 'demo';
    const FAKE_VALUE_TAX_PERCENT = 35;
    const FAKE_INTEGRATION_TAX_PERCENT = 10;
    const DEMO_USER_LOGIN = 'demoaccount';
    const INTEGRATION_NAME_PREFIX = 'c4r_demo';
    const DEFAULT_ORDER_ID = 106;
    private $entityManager;
    private $items = []; //demo item data

    protected function configure()
    {
        $this
            ->setName('app:generate_demo_account_partner')
            ->setDescription('This command generate demo account partner example: app:generate_demo_account_partner')
            ->addOption('deleted', null, InputOption::VALUE_OPTIONAL, 'to delete --deleted=1 option', false)
            ->addOption('integrationId', null, InputOption::VALUE_OPTIONAL, 'delete single --integrationId=123', null)
        ;

        $this->items[9841206662] = [
            'id' => 9841206662,
            'market_name' => 'StatTrak™ UMP-45 | Первобытный саблезуб (Немного поношенное)',
            'market_hash_name' => 'StatTrak™ UMP-45 | Primal Saber (Minimal Wear)',
            'price' => 1013.33,
            'price_raw' => 1013.33,
            'icon_url' => 'https://steamcommunity-a.akamaihd.net/economy/image/-9a81dlWLwJ2UUGcVs_nsVtzdOEdtWwKGZZLQHTxDZ7I56KU0Zwwo4NUX4oFJZEHLbXH5ApeO4YmlhxYQknCRvCo04DEVlxkKgpoo7e1f1Jf0Ob3ZDBSuImJhJKCmvb4ILrTk3lu5Mx2gv2Po9v3jVLt-hJoYG7wINKTdwI7YF6G_FTtxeznjZG9vc_LzHU3uCAm7GGdwUIwVIf-Gg/100x100',
            'color' => null,
            'rarity' => null,
            'marketable' => null,
            'tradeable' => null,
            'acceptable' => 1,
            'type' => 1,
            'orig_price'    => 14.00,
            'app_id' => 730,
        ];

        $this->items[9840122979] = [
            'id' => 9840122979,
            'market_name' => 'StatTrak™ Five-SeveN | Медная галактика (После полевых испытаний)',
            'market_hash_name' => 'StatTrak™ Five-SeveN | Copper Galaxy (Field-Tested)',
            'price' => 166.98,
            'price_raw' => 166.98,
            'icon_url' => 'https://steamcommunity-a.akamaihd.net/economy/image/-9a81dlWLwJ2UUGcVs_nsVtzdOEdtWwKGZZLQHTxDZ7I56KU0Zwwo4NUX4oFJZEHLbXH5ApeO4YmlhxYQknCRvCo04DEVlxkKgposLOzLhRlxfbGTjxP09-5hJCOhcjyP77SnXhu5cB1g_zMu4igjQC1rhBsYD2nIoOTJFU7ZFmEqAe9xru5g560tZnInyA3vSMnsHjD30vgzU7onQU/100x100',
            'color' => null,
            'rarity' => null,
            'marketable' => null,
            'orig_price'    => 3.00,
            'tradeable' => null,
            'acceptable' => 1,
            'type' => 1,
            'app_id' => 730,
        ];

        $this->items[11300438826] = [
            'id'    => 11300438826,
            'market_name'   => 'Desert Eagle | Нага (Закаленное в боях)',
            'market_hash_name'  => 'Desert Eagle | Naga (Battle-Scarred)',
            'price' => 15.52,
            'price_raw' => 15.52,
            "app_id"    =>730,
            "orig_price"    =>0.43,
            "rate_value"    =>60.16,
        ];

        $this->items[6955533604] = [
            "id"    => "6955533604",
            "market_name"    => "Prey of the Demonic Vessel",
            "market_hash_name"    => "Prey of the Demonic Vessel",
            "price"    => "0.81",
            "price_raw"    => 0.81,
            "icon_url"    => "https:\/\/steamcommunity-a.akamaihd.net\/economy\/image\/-9a81dlWLwJ2UUGcVs_nsVtzdOEdtWwKGZZLQHTxDZ7I56KW1Zwwo4NUX4oFJZEHLbXK9QlSPcUzqxpZSEPeCOWh28bSXV5xGgVVtLuaOA9vxv_MdC8N7dC6nYGFlPLLMrnTl1RC4Mpkhu3E58KiilXhqEc6Mmmid4KSegVrNVzW_lfqyLy905Dv6cidwHowuyJwtyvdgVXp1jQeXO7t\/100x100",
            "color"    => null,
            "rarity"    => null,
            "marketable"    => true,
            "tradeable"    => null,
            "acceptable"    => true,
            "type"    => 1,
            "app_id"    => 570,
            "orig_price"    => 0.03,
            "rate_value"    => 60.16,
        ];

        $this->items[12086244957] = [
            "id" => "12086244957",
            "market_name" => "Quiver of the Shadowcat",
            "market_hash_name" => "Quiver of the Shadowcat",
            "price" => "0.81",
            "price_raw" => 0.81,
            "icon_url" => "https:\/\/steamcommunity-a.akamaihd.net\/economy\/image\/-9a81dlWLwJ2UUGcVs_nsVtzdOEdtWwKGZZLQHTxDZ7I56KW1Zwwo4NUX4oFJZEHLbXK9QlSPcU2uxRKA13FTvKoxfDcVWJgLQFopbKkLwh30PLcPixX5cqzhr-EkcjgO77uhWNQ7MpmiejVu96tjgPlrkQ9Nm-mdoXHIQY5ZVDWqVi-x-3q1Ja_7cufzSQ17HQh4nnD30vg-kB5TBM\/100x100",
            "color" => null,
            "rarity" => null,
            "marketable" => true,
            "tradeable" => null,
            "acceptable" => true,
            "type" => 1,
            "app_id" => 570,
            "orig_price" => 0.03,
            "rate_value" => 60.16,
        ];
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->entityManager = $this->getContainer()->get('doctrine')->getManager();
            if ($input->getOption('deleted')) {
                return $this->deletedAccounts($input, $output);
            }

            $output->writeln('<info>Start '.$this->getName().' command</info>');
            $container = $this->getContainer();
            $integrationService = $container->get('app.integration_service');
            $depositService = $container->get('app.deposit_service');
            $userManager = $container->get('fos_user.user_manager');
            $user = $userManager->createUser();
            $username = $this->findActualDemoLogin();

            if ($user instanceof UserInterface) {
                $user->setUsername($username);
                $userExists = $userManager->findUserByUsername($username);
                if (!empty($userExists) && $userExists instanceof UserInterface) {
                    throw new \Exception('User '.$username.' exists');
                }
                $user
                    ->setEmail($username.'@skins4real.com')
                    ->setPlainPassword('password')
                    ->setEnabled(true)
                    ->setRoles([User::ROLE_INTEGRATOR, User::ROLE_DEMO]);
                $userManager->updateUser($user);
                $output->writeln('<info>user created id '.$user->getUsername().'</info>');
            }

            $intergationName = self::INTEGRATION_NAME_PREFIX.$username;
            $integrationExists = $integrationService->findByName($intergationName);
            if (!empty($integrationExists) && $integrationExists instanceof Integration) {
                throw new \Exception('Integration '.$intergationName.' exists');
            }

            $integration = new Integration();
            $integration->setName('c4r_'.$username);
            $integration->setCreated(new \DateTime('now'));
            $integration->setPushbackUrl(self::PUSHBACK_URL);
            $integration->setSuccessUrl(self::SUCCESS_URL);
            $integration->setIntegrationTaxPercent(self::FAKE_INTEGRATION_TAX_PERCENT);
            $integration->setValueTaxPercent(self::FAKE_VALUE_TAX_PERCENT);
            $integration->setPrivateKey(self::FAKE_PRIVATE_KEY);
            $integration->setPublicKey(self::FAKE_PUBLIC_KEY.$integration->getName());
            $integration->setIsDemo(true);
            $integration = $integrationService->create($integration);
            $output->writeln('<info>integration created id '.$integration->getId().'</info>');

            //set integration of user entity
            $user->setIntegration($integration);
            $userManager->updateUser($user);

            //today timestamp
            $startDatetime = time();
            //2 month ago
            $endDatetime = $startDatetime - (3600 * 24 * 60);
            $lastDeposit = $depositService->getLast(1);
            $orderId = 1;
            $allDepositStatuses = [Deposit::STATUS_NEW, Deposit::STATUS_COMPLETED, Deposit::STATUS_PENDING, Deposit::STATUS_ERROR_BOT];
            //iterations everyday of this period
            $container->get('doctrine.orm.default_entity_manager')->getClassMetadata(Deposit::class)->setLifecycleCallbacks([]);
            while ($endDatetime <= $startDatetime) {
                //iterations of all statuses of Deposit
                foreach ($allDepositStatuses as $status) {
                    //generate 10 items of single status
                    $step = 1;
                    while ($step < 11) {
                        $cloneDeposit = clone $lastDeposit[0];
                        if ($cloneDeposit instanceof Deposit) {
                            $cloneDeposit
                                ->setId(null)
                                ->setItems([])
                                ->setCurrency(Deposit::CURRENCY_RUB)
                                ->setCreated(new \DateTime(date('Y-m-d', $endDatetime)))
                                ->setIntegration($integration)
                                ->setSteamId(rand(100000, 600000))
                                ->setStatus($status)
                                ->setOrderId($orderId++)
                            ;
                            if ($status !== Deposit::STATUS_NEW) {
                                $value = rand(0, 1000);
                                $noTaxValue = $orderId + rand(0, 10) / 10;
                                $cloneDeposit
                                    ->setItems($this->items)
                                    ->setNoTaxValue($noTaxValue)
                                    ->setValue($value)
                                    ->setValueCsgo(round($value/2, 2))
                                    ->setValueDota(round($value/2, 2))
                                    ->setNoTaxValueCsgo(round($noTaxValue/2))
                                    ->setNoTaxValueDota(round($noTaxValue/2))
                                ;
                            }
                            $depositService->create($cloneDeposit);
                        }
                        ++$step;
                    }
                }

                $output->writeln('<info>Create deposits of date '.date('Y-m-d', $endDatetime).'</info>');
                $endDatetime += 24 * 3600;
            }

            $this->entityManager->flush();

            $output->writeln('<info>Demo account created success</info>');
        } catch (\Exception $exception) {
            echo  $output->writeln('<error>'.$exception->getMessage().'</error>');
        }
    }

    /**
     * method find actual demo user login by mask demoaccount{\D*}.
     *
     * @return string
     */
    private function findActualDemoLogin()
    {
        $userManager = $this->getContainer()->get('fos_user.user_manager');
        $login = self::DEMO_USER_LOGIN;
        $counter = 1;
        while (true) {
            try {
                $lastDemoUser = $userManager->findUserByUsername($login);
                if (!empty($lastDemoUser) && $lastDemoUser instanceof UserInterface) {
                    $login = sprintf('%s%d', self::DEMO_USER_LOGIN, $counter++);
                    continue;
                }

                return $login;
            } catch (\Exception $exception) {
                break;
            }
        }
    }

    /**
     * method remove demo data.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    private function deletedAccounts(InputInterface $input, OutputInterface $output)
    {
        //finded all integration of idDemo =  true
        $container = $this->getContainer();
        $integrationService = $container->get('app.integration_service');
        $depositService = $container->get('app.deposit_service');
        $integrationDebitService = $container->get('app.integration_debit_service');
        $integrationBalanceService = $container->get('app.integration_balance_service');
        $userManager = $container->get('fos_user.user_manager');

        $criteria = ['isDemo' => true];
        if ($input->getOption('integrationId')) {
            $criteria['id'] = filter_var($input->getOption('integrationId'), FILTER_VALIDATE_INT);
        }
        $demoIntegrations = $integrationService->findBy($criteria);
        if (!empty($demoIntegrations) and is_array($demoIntegrations)) {
            foreach ($demoIntegrations as $integration) {
                if ($integration instanceof Integration) {
                    if (!$integration->getIsDemo()) {
                        continue;
                    }

                    $user = $userManager->findUserBy(['integration' => $integration]);
                    if (!empty($user) && $user instanceof UserInterface) {
                        //if user not have ROLE_DEMO then continue iteration
                        if (!$user->hasRole(User::ROLE_DEMO)) {
                            continue;
                        }
                        $output->writeln('<info>Remove user #'.$user->getId().'</info>');
                        $userManager->deleteUser($user);
                    }

                    $depositService->deleteByIntegration($integration);
                }
                $output->writeln('<info>Remove integration #'.$integration->getId().'</info>');
                //delete balance
                $integrationBalanceService->deleteByIntegration($integration);
                //delete debit
                $integrationDebitService->deleteByIntegration($integration);
                //delete integration
                $integrationService->remove($integration);
            }
        }
    }
}
