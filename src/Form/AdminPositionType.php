<?php

namespace App\Form;

use App\Entity\SysPosition;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminPositionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label')
            ->add('upperID')
            ->add('publicLabel')
            ->add('isDefault')
            ->add('permissiongroups')
            ->add('permissionFromRoll')
            ->add('constractor')
            ->add('userID')
            ->add('defaultArea')
            ->add('roll')
            ->add('submit',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SysPosition::class,
        ]);
    }
}
