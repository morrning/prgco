<?php

namespace App\Form;

use App\Entity\HRMLetterOutCountry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Service;

class HRMLetterOutCountryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $jdate = new Service\Jdate();
        $builder
            ->add('letterNum', TextType::class,['label'=>'شماره نامه'])
            ->add('letterStartDate',Type\JdateType::class,['label'=>' شروع اعتبار','data'=>$jdate->jdate('Y/m/d',time())])
            ->add('letterEndDate',Type\JdateType::class,['label'=>'پایان اعتبار','data'=>$jdate->jdate('Y/m/d',time() + (3600*24*90))])
            ->add('letterSource',TextType::class,['label'=>'مرجع صدور'])
            ->add('submit', SubmitType::class,['label'=>'ثبت اطلاعات مکاتبه'])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => HRMLetterOutCountry::class,
        ]);
    }
}
