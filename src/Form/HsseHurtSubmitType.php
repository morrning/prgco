<?php

namespace App\Form;

use App\Entity\HsseHurt;
use App\Form\Type\JdateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HsseHurtSubmitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('htype', ChoiceType::class, [
                'label'=>'نوع حادثه',
                'choices'  => [
                    'شبه حادثه' => 'شبه حادثه',
                    'سقوط افراد یا اشیا' => 'سقوط افراد یا اشیا',
                    'برخورد' => 'برخورد',
                    'برق گرفتگی' => 'برق گرفتگی',
                    'انفجار' => 'انفجار',
                    'حوادث جاده‌ای' => 'حوادث جاده‌ای',
                    'غیر مترقبه' => 'غیر مترقبه',
                    'آتش سوزی' => 'آتش سوزی',
                    'غرق شدگی' => 'غرق شدگی',
                    'مسمومیت' => 'مسمومیت',
                    'تماس با مواد زیان آور' => 'تماس با مواد زیان آور',
                    'جا به جایی بار' => 'جا به جایی بار',
                    'سر خوردن' => 'سر خوردن',
                    'ریزش' => 'ریزش',
                    'گیر کردن' => 'گیر کردن',
                    'زیست محیطی' => 'زیست محیطی',
                    'درگیری' => 'درگیری',
                    'متفرقه' => 'متفرقه',
                ],
            ])
            ->add('hgraid', ChoiceType::class, [
                'label'=>'درجه حادثه',
                'choices'  => [
                    '0' => 0,
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4' => 4,
                    '5' => 5,
                    '6' => 6
                ],
            ])
            ->add('hdate',JdateType::class,['label'=>'تاریخ وقوع حادثه'])
            ->add('htime',TimeType::class,[
                'label'=>'ساعت وقوع حادثه',
                'input'=>'string',
                'html5'=>true
            ])
            ->add('title',TextType::class,['label'=>'موضوع حادثه'])
            ->add('des',TextareaType::class,['label'=>'شرح کامل حادثه'])
            ->add('location',TextType::class,['label'=>'محل وقوع حادثه'])
            ->add('doctorDoing',TextareaType::class,['label'=>'اقدامات پزشک برای مصدومان'])
            ->add('submit',SubmitType::class,['label'=>'ثبت حادثه'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => HsseHurt::class,
        ]);
    }
}
