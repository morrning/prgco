<?php

namespace App\Form;

use App\Entity\HsseTool;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HSSEToolType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('toolName',ChoiceType::class,['label'=>'نوع تجهیزات',
                'choices'  => [
                    'دستکش کار' => 'دستکش کار',
                    'لباس کار' => 'لباس کار',
                    'کلاه ایمنی' => 'کلاه ایمنی',
                    'کفش ایمنی'=>'کفش ایمنی'
                ],
            ])
            ->add('num',NumberType::class,['label'=>'تعداد'])
            ->add('submit',SubmitType::class,['label'=>'ثبت '])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => HsseTool::class,
        ]);
    }
}
