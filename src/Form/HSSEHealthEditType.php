<?php

namespace App\Form;

namespace App\Form;

use App\Entity\HsseHealth;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
            ->add('whyCome', ChoiceType::class, [
                'label'=>'دلیل مراجعه',
                'choices'  => [
                    'بیماری' => 'بیماری',
                    'حادثه' => 'حادثه',
                ],
            ])
            ->add('des',TextareaType::class,['label'=>'توضیحات بیشتر'])
            ->add('cause',TextType::class,['label'=>'تشخیص بهداری'])
            ->add('drugs',TextType::class,['required'=> false,'label'=>'داروهای تجویز شده','attr'=>['class'=>'taginput']])
            ->add('services',TextType::class,['required'=> false,'label'=>'خدمات ارائه شده','attr'=>['class'=>'taginput']])
            ->add('AMP', ChoiceType::class, [
                'label'=>'استفاده از آمبولانس',
                'choices'  => [
                    'خیر' => 'خیر',
                    'بله' => 'بله',
                ],
            ])
            ->add('result', ChoiceType::class, [
                'label'=>'نتیجه اقدامات',
                'choices'  => [
                    'در حال درمان' => 'در حال درمان',
                    'بازگشت به کار' => 'بازگشت به کار',
                    'ارجاع به پزشک متخصص'=>'ارجاع به پزشک متخصص',
                    'ارجاع به بیمارستان' => 'ارجاع به بیمارستان'
                ],
            ])
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
