<?php

namespace App\Form;

use App\Entity\HRMLetterOutCountry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HRMLetterOutCountryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('letterNum')
            ->add('letterStartDate')
            ->add('letterEndDate')
            ->add('letterSource')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => HRMLetterOutCountry::class,
        ]);
    }
}
