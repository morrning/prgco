<?php

namespace App\Form;

use App\Entity\SuuportTicket;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SupportTicketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('mainTicket')
            ->add('subject')
            ->add('body')
            ->add('dateSubmit')
            ->add('UID')
            ->add('submitter')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SuuportTicket::class,
        ]);
    }
}
