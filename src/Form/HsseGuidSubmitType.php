<?php

namespace App\Form;

use App\Entity\HsseGuid;
use App\Form\Type\JdateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HsseGuidSubmitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title',TextType::class,['label'=>'موضوع کلاس:'])
            ->add('teacher',TextType::class,['label'=>'مدرس آموزش دهنده:'])
            ->add('dateDoing',JdateType::class,['label'=>'تاریخ برگزاری:'])
            ->add('des',TextareaType::class,['label'=>'توضیحات:'])
            ->add('submit',SubmitType::class,['label'=>'ثبت دوره'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => HsseGuid::class,
        ]);
    }
}
