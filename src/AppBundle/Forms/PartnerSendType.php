<?php
namespace AppBundle\Forms;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class PartnerSendType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setAction($options['action'])
            ->setMethod('post')
            ->add('username', TextType::class, ['attr' => ['placeholder' => 'ваше имя']])
            ->add('company', TextType::class, ['attr' => ['placeholder' => 'ваша компания']])
            ->add('email', EmailType::class, ['attr' => ['placeholder' => 'ваш email']])
            ->add('phone', TextType::class, ['attr' => ['placeholder' => 'ваш телефон', 'pattern' => "[0-9.\(\)\+\-\s]+" ]]);

    }
}
