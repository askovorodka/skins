<?php

namespace AppBundle\Controller\REST;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TokenController extends Controller
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws \Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException]
     * @Method("POST")
     * @Route("/token_auth", name="token_auth")
     */
    public function tokenAuthorizationAction(Request $request)
    {
        $creds = $request->getContent();
        if (empty($creds)) {
            return new JsonResponse(['status' => 'fail', 'message' => 'Need login and password']);
        }

        $params = json_decode($creds, true);
        $username = $params['username'];
        $password = $params['password'];

        /** @var User $user */
        $user = $this->get('doctrine.orm.entity_manager')->getRepository(User::class)
            ->findOneBy(['username' => $username]);

        if (!$user) {
            return new JsonResponse(['status' => 'fail', 'message' => 'Unknown username']);
        }

        if (!in_array(User::ROLE_INTEGRATOR, $user->getRoles())) {
            return new JsonResponse(['status' => 'fail', 'message' => 'Forbidden!']);
        }

        if (!$this->get('security.password_encoder')->isPasswordValid($user, $password)) {
            throw $this->createAccessDeniedException();
        }

        $token = $this->get('lexik_jwt_authentication.encoder')
            ->encode(['username' => $user->getUsername()]);

        $this->get('logger')->crit('partner login!', [$user->getUsername(), $user->getIntegration(), $request->getClientIp()]);

        return new JsonResponse(['token' => $token]);
    }
}
