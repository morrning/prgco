<?php

namespace App\Form;

namespace App\Form;

use App\Entity\HsseHealth;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HSSEHealthEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('des',TextareaType::class,['label'=>'توضیحات بیشتر'])
            ->add('cause',TextType::class,['label'=>'تشخیص بهداری'])
            ->add('whyCome',TextType::class,['label'=>'دلیل مراجعه'])
            ->add('drugs',TextType::class,['label'=>'داروهای تجویز شده','attr'=>['class'=>'taginput']])
            ->add('submit',SubmitType::class,['label'=>'ویرایش وضعیت'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
