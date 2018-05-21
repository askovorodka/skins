<?php

namespace AppBundle\Service;

use AppBundle\Entity\Deposit;
use AppBundle\Entity\Integration;
use AppBundle\Entity\IntegrationBalance;
use AppBundle\Entity\IntegrationDebit;
use AppBundle\Exception\IncorrectHashException;
use AppBundle\Exception\IntegrationNotFoundException;
use Doctrine\ORM\EntityManager;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use Monolog\Logger;

class IntegrationService
{
    const PUSH_STATUS_SUCCESS = 1;
    const PUSH_STATUS_ALREADY_ACCEPTED = 2;
    const PUSH_STATUS_INCORRECT_SIGN = 3;
    const PUSH_STATUS_UNKNOWN_ERROR = 4;
    const PUSH_STATUS_INCORRECT_DATA = 5;
    const PUSH_STATUS_LOCK = 6;
    const PUSH_STATUS_ORDER_NOT_FOUND = 7;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param EntityManager $entityManager
     * @param Logger        $logger
     */
    public function __construct(EntityManager $entityManager, Logger $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    /**
     * @param $publicKey
     *
     * @return Integration
     *
     * @throws IntegrationNotFoundException
     */
    public function getIntegrationByPublicKey($publicKey)
    {
        $integration = $this->entityManager->getRepository(Integration::class)->findOneBy(['publicKey' => $publicKey]);
        if ($integration === null) {
            $this->logger->alert('Integration not found', [$publicKey]);
            throw new IntegrationNotFoundException();
        }

        return $integration;
    }

    /**
     * @param Deposit $deposit
     *
     * @return bool
     */
    public function sendPushBack(Deposit $deposit)
    {
        $deposit->setPushbackCreated(new \Datetime());
        $integration = $deposit->getIntegration();
        $request = [
            'currency' => $deposit->getCurrency(),
            'amount' => $deposit->getValue(),
            'transaction_id' => $deposit->getId(),
            'order_id' => $deposit->getOrderId(),
        ];
        $request['sign'] = $this->createHash($integration, $request);
        if ($integration->getHttpAuthUsername()) {
            $post['auth'] = [$integration->getHttpAuthUsername(), $integration->getHttpAuthPassword()];
        }
        $post['headers'] = ['User-Agent' => 'HEYDRUPAL'];
        $post['form_params'] = $request;
        $httpClient = new Client();
        $result = null;
        try {
            $this->logger->crit('send_pushback',[
                'url' => $integration->getPushbackUrl(),
                'post'  => $post,
            ]);

            $result = json_decode($httpClient->request('POST', $integration->getPushbackUrl(), $post)->getBody()->getContents(), true);
            $this->logger->info('Push response!', [$result]);

            $statusMap = [
                self::PUSH_STATUS_SUCCESS,
                self::PUSH_STATUS_ALREADY_ACCEPTED,
                self::PUSH_STATUS_INCORRECT_SIGN,
                self::PUSH_STATUS_UNKNOWN_ERROR,
                self::PUSH_STATUS_INCORRECT_DATA,
                self::PUSH_STATUS_LOCK,
                self::PUSH_STATUS_ORDER_NOT_FOUND,
            ];

            if (!in_array($result['status'], $statusMap)) {
                $this->logger->critical('push back failed! unexpected status code', [$result, $request]);

                return false;
            }
            $deposit->setPushStatus($result['status']);
            $this->entityManager->flush($deposit);

            return true;
        } catch (ClientException $e) {
            $this->logger->critical('Push back failed!', [
                $integration->getName(),
                $request,
                $e->getMessage(),
                $e->getCode(),
                $result]);

            return false;
        } catch (ConnectException $e) {
            $this->logger->critical('Push back failed', [$integration->getName(), $request, $e->getMessage(), $e->getCode(), $result]);

            return false;
        } catch (ServerException $e) {
            $this->logger->critical('Push back failed', [$integration->getName(), $request, $e->getMessage(), $e->getCode(), $result]);

            return false;
        }
    }

    /**
     * @param Integration $integration
     * @param $query
     *
     * @return string
     */
    public function createHash(Integration $integration, $query)
    {
        ksort($query);

        $sign = '';
        foreach ($query as $key => $value) {
            $sign .= $key.':'.$value.';';
        }

        return hash_hmac('sha1', $sign, $integration->getPrivateKey());
    }

    /**
     * @param Integration $integration
     * @param $query
     * @param $inputHash
     *
     * @throws IncorrectHashException
     */
    public function checkHash(Integration $integration, $query, $inputHash)
    {
        unset($query['sign']);
        unset($query['lang']);
        $hash = $this->createHash($integration, $query);

        if (!hash_equals($hash, $inputHash)) {
            throw new IncorrectHashException('incorrect_sign');
        }
    }

    /**
     * @param Integration $integration
     *
     * @return array
     */
    public function getIntegrationBalance(Integration $integration)
    {
        $integrationBalances = $this->entityManager->getRepository(IntegrationBalance::class)->findBy(['integration' => $integration]);
        $result = [];
        foreach ($integrationBalances as $balance) {
            $result[$balance->getCurrency()] = $balance->getBalance();
        }

        return $result;
    }

    public function recountIntegrationsBalance()
    {
        $integrations = $this->entityManager->getRepository(Integration::class)->findAll();
        foreach ($integrations as $integration) {
            $this->recountIntegrationBalance($integration);
        }
    }

    /**
     * @param $integration
     */
    public function recountIntegrationBalance(Integration $integration)
    {
        $depositRepository = $this->entityManager->getRepository(Deposit::class);
        $debitRepository = $this->entityManager->getRepository(IntegrationDebit::class);
        $profits = $depositRepository->getDepositValueSumByIntegration($integration);
        $debits = $debitRepository->getDebitsByIntegration($integration);
        $currencies = array_keys($profits);

        $currencyBalance = [];
        foreach ($currencies as $currency) {
            $profit = $profits[$currency]['profit'] ?? 0.00;
            $this->logger->crit('math profit', [$profit]);
            $debit = $debits[$currency]['debit'] ?? 0.00;
            $this->logger->crit('math debit', [$debit]);
            $profit = bcsub($profit, $debit, 2);
            $this->logger->crit('math final', [$profit]);
            $currencyBalance[$currency] = bcmul($profit, (1 - ($integration->getIntegrationTaxPercent() / 100)), 2);
        }

        foreach ($currencyBalance as $currency => $balance) {
            $integrationBalance = $this->getOrCreateIntegrationBalance($integration, $currency);

            $integrationBalance
                ->setBalance($balance);

            $this->entityManager->flush($integrationBalance);
        }
    }

    /**
     * @param $integration
     * @param $currency
     *
     * @return IntegrationBalance
     */
    public function getOrCreateIntegrationBalance($integration, $currency)
    {
        $integrationBalance = $this->entityManager->getRepository(IntegrationBalance::class)
            ->findOneBy(['integration' => $integration, 'currency' => $currency]);
        if ($integrationBalance === null) {
            $integrationBalance = new IntegrationBalance();
            $integrationBalance
                ->setCurrency($currency)
                ->setIntegration($integration)
            ;

            $this->entityManager->persist($integrationBalance);
        }

        return $integrationBalance;
    }

    public static function getIntegrationDebitStatuses()
    {
        return [
            IntegrationDebit::STATUS_COMPLETED => IntegrationDebit::STATUS_COMPLETED,
            IntegrationDebit::STATUS_NEW => IntegrationDebit::STATUS_NEW,
            IntegrationDebit::STATUS_PENDING => IntegrationDebit::STATUS_PENDING,
            IntegrationDebit::STATUS_REJECTED => IntegrationDebit::STATUS_REJECTED,
        ];
    }

    /**
     * @return Integration[]
     */
    public function getAllIntegrations()
    {
        return $this->entityManager->getRepository(Integration::class)->findBy(['isDemo' => null]);
    }

    /**
     * method created new intergation entity.
     *
     * @param Integration $integration
     *
     * @return Integration
     */
    public function create(Integration $integration): Integration
    {
        $this->entityManager->persist($integration);
        $this->entityManager->flush();

        return $integration;
    }

    /**
     * method find single entity by name.
     *
     * @param string $name
     *
     * @return null|object
     */
    public function findByName(string $name)
    {
        return $this->entityManager->getRepository(Integration::class)->findOneBy(['name' => $name]);
    }

    /**
     * method finded rows by criteria.
     *
     * @param array $criteria
     * @param array $sort
     *
     * @return array
     */
    public function findBy(array $criteria = [], array $sort = ['id' => 'desc'], $limit = null)
    {
        return $this->entityManager->getRepository(Integration::class)->findBy($criteria, $sort, $limit);
    }

    public function remove(Integration $integration)
    {
        $this->entityManager->remove($integration);
        $this->entityManager->flush();
    }

    /**
     * get by id.
     *
     * @param $id
     *
     * @return Integration|null|object
     */
    public function getById($id)
    {
        return $this->entityManager->getRepository(Integration::class)->find($id);
    }
}
