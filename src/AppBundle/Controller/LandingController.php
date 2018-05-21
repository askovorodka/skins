<?php

namespace AppBundle\Controller;

use AppBundle\Forms\AdSendType;
use AppBundle\Forms\PartnerSendType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * index page controller
 */
class LandingController extends Controller
{

    /**
     * landing
     * @param Request $request
     * @Route("/", name="landing_page")
     * @ Security("has_role('ROLE_ADMIN')")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $loginForm = $this->createFormBuilder()
            ->setAction($this->get('router')->generate('token_auth'))
            ->add('username', TextType::class, ['attr'=>['placeholder' => 'логин']])
            ->add('password', PasswordType::class, ['attr'=> ['placeholder' => 'пароль']])
            ->getForm();

        $partnerForm = $this->createForm(PartnerSendType::class, null, ['action' => $this->get('router')->generate('partner_send_mail')]);
        $adForm = $this->createForm(AdSendType::class, null, ['action' => $this->get('router')->generate('ad_send_mail')]);

        return $this->render('landing/landing.html.twig', [
            'login_form'    => $loginForm->createView(),
            'partner_form'  => $partnerForm->createView(),
            'partner_site_key'  => $this->getParameter('captcha_partner_site_key'),
            'ad_form'       => $adForm->createView(),
            'ad_site_key'  => $this->getParameter('captcha_ad_site_key'),
        ]);
    }

    /**
     * @param Request $request
     * @Route("/partner_send/", name="partner_send_mail")
     * @Method("POST")
     */
    public function partnerSendAction(Request $request)
    {

        $form = $this->createForm(PartnerSendType::class);
        $form->handleRequest($request);
        $captchaService = $this->get('app.captcha_service');

        try {
            if ($form->isSubmitted()) {
                if (!$captchaService->verify($request->get('g-recaptcha-response'), $this->getParameter('captcha_partner_secret_key'))){
                    throw new \Exception('Ошибка ввода капчи');
                }

                if ($form->isValid()) {
                    $notifyService = $this->get('app.notification_service');
                    $to = $this->getParameter('notification_email');
                    $from = $this->getParameter('email.feedback.from');
                    $username = $form->get('username')->getData();
                    $company = $form->get('company')->getData();
                    $email = $form->get('email')->getData();
                    $phone = $form->get('phone')->getData();
                    $message = "<p>Имя: {$username}</p>";
                    $message .= "<p>Компания: {$company}</p>";
                    $message .= "<p>Email: {$email}</p>";
                    $message .= "<p>Телефон: {$phone}</p>";

                    $notifyService->notifyByEmail("Заявка с формы Подключение к сервису Skins4Real", $from, $to, $message);
                }
            }
        } catch(\Exception $exception) {
            return new JsonResponse(['status' => 'error', 'message' => $exception->getMessage()]);
        }

        return new JsonResponse(['status' => 'success', 'message' => 'email sended']);
    }

    /**
     * @param Request $request
     * @Route("/ad_send/", name="ad_send_mail")
     * @Method("POST")
     */
    public function adSendAction(Request $request)
    {
        $form = $this->createForm(AdSendType::class);
        $form->handleRequest($request);
        $captchaService = $this->get('app.captcha_service');

        try {
        if ($form->isSubmitted()) {
            if (!$captchaService->verify($request->get('g-recaptcha-response'), $this->getParameter('captcha_ad_secret_key'))){
                throw new \Exception('Ошибка ввода капчи');
            }

            if ($form->isValid()) {
                $notifyService = $this->get('app.notification_service');
                $to = $this->getParameter('notification_email');
                $from = $this->getParameter('email.feedback.from');
                $username = $form->get('username')->getData();
                $company = $form->get('company')->getData();
                $email = $form->get('email')->getData();
                $phone = $form->get('phone')->getData();
                $message = "<p>Имя: {$username}</p>";
                $message .= "<p>Компания: {$company}</p>";
                $message .= "<p>Email: {$email}</p>";
                $message .= "<p>Телефон: {$phone}</p>";

                $notifyService->notifyByEmail("Заявка с формы Совместные рекламные кампании", $from, $to, $message);
            }
        }
        } catch(\Exception $exception) {
            return new JsonResponse(['status' => 'error', 'message' => $exception->getMessage()]);
        }


        return new JsonResponse(['status' => 'success', 'message' => 'email sended']);

    }

}
