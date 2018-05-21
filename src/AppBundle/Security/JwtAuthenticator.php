<?php

namespace AppBundle\Security;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\AuthorizationHeaderTokenExtractor;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class JwtAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var JWTEncoderInterface
     */
    private $jwtEncoder;

    /**
     * JwtAuthenticator constructor.
     *
     * @param EntityManager       $em
     * @param JWTEncoderInterface $jwtEncoder
     */
    public function __construct(EntityManager $em, JWTEncoderInterface $jwtEncoder)
    {
        $this->em = $em;
        $this->jwtEncoder = $jwtEncoder;
    }

    /**
     * @param Request                      $request
     * @param AuthenticationException|null $authException
     *
     * @return JsonResponse
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new JsonResponse('Auth header required', 401);
    }

    public function getCredentials(Request $request)
    {
        if (!$request->headers->has('Authorization')) {
            return;
        }

        $extractor = new AuthorizationHeaderTokenExtractor(
            'Bearer',
            'Authorization'
        );

        $token = $extractor->extract($request);

        if (!$token) {
            return;
        }

        return $token;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        try {
            $data = $this->jwtEncoder->decode($credentials);
        } catch (JWTDecodeFailureException $e) {
            throw new AuthenticationException('token expired');
        }

        if (!$data) {
            return;
        }

        $username = $data['username'];

        $user = $this->em->getRepository(User::class)
            ->findOneBy(['username' => $username]);

        if (!$user) {
            return;
        }

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new JsonResponse([
            'message' => $exception->getMessage(),
        ], 401);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return;
    }

    public function supportsRememberMe()
    {
        return false;
    }
}
