<?php

namespace App\Form;

use App\Entity\HssePenalty;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HSSEPenaltyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('work',TextType::class,['label'=>'موضوع کار انجام شده'])
            ->add('penaltyType',ChoiceType::class,['label'=>'نوع جریمه',
                'choices'  => [
                    'جریمه نقدی' => 'جریمه نقدی',
                    'اخطار' => 'اخطار',
                    'اخراج' => 'اخراج',
                ],
            ])
            ->add('des',TextareaType::class,['label'=>'توضیحات'])
            ->add('place',TextType::class,['label'=>'محل ارتکاب تخلف'])
            ->add('price',TextType::class,['label'=>'رقم جریمه نقدی','required'=>false])
            ->add('submit',SubmitType::class,['label'=>'ثبت جریمه'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => HssePenalty::class,
        ]);
    }
}
