<?php
namespace AppBundle\Forms;

use AppBundle\Entity\Deposit;
use AppBundle\Entity\Integration;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class AdminStatisticsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('date_from', DateType::class,[
            'widget' => 'single_text',
            'input' => 'datetime',
            'format' => 'yyyy-MM-dd',
            'data' => $options['data']['dateFrom'],
            'required' => false,
            ])
            ->add('date_to', DateType::class, array(
                'widget' => 'single_text',
                'input' => 'datetime',
                'format' => 'yyyy-MM-dd',
                'data' => $options['data']['dateTo'],
                'required' => false,
            ))
            ->add('group_by', ChoiceType::class,[
                'choices' => [
                    'day' => 'day',
                    'month' => 'month',
                    'year' => 'year',
                    'hour' => 'hour',
                ],
                'data' => $options['data']['groupBy'],
            ])
            ->add('integration', EntityType::class,[
                'data' => $options['data']['integration'],
                'class' => Integration::class,
                'choice_label'  => function($integration){
                    return $integration->getName();
                },
                'query_builder' => function(EntityRepository $repository) {
                    return $repository
                        ->createQueryBuilder('i')
                        ->where('i.isDemo IS NULL')
                        ->orderBy('i.name','asc');
                }
            ])
            ->add('currency', ChoiceType::class, [
                'choices'   => [
                    'all' => 0,
                    Deposit::CURRENCY_RUB => Deposit::CURRENCY_RUB,
                    Deposit::CURRENCY_USD => Deposit::CURRENCY_USD,
                ],
                'data'  => $options['data']['currency'],
            ]);


    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $new_choice = new ChoiceView(array(), '', 'all');
        $view->children['integration']->vars['choices'][] = $new_choice;
    }
}