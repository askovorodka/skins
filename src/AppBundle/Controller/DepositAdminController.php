<?php
/**
 * Created by PhpStorm.
 * User: ahimas
 * Date: 03.10.16
 * Time: 17:32
 */

namespace AppBundle\Controller;


use AppBundle\Exception\DepositPushBackException;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DepositAdminController extends CRUDController
{
    public function sendPushBackAction($id)
    {
        $object = $this->admin->getSubject();

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        try {
            $this->get('app.deposit_service')->confirmTrade($id);
            $this->addFlash('sonata_flash_success', 'Pushback sent');
        } catch (DepositPushBackException $e) {
            $this->addFlash('sonata_flash_error', 'Pushback could not be sent! '.$e->getMessage());
        }

        return new RedirectResponse($this->admin->generateUrl('list'));
    }
}