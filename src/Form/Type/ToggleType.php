<?php
/**
 * Created by PhpStorm.
 * User: babak
 * Date: 17/06/2018
 * Time: 09:25 AM
 */

namespace App\Form\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class ToggleType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {

    }
    public function getParent()
    {
        return CheckboxType::class;
    }

    public function getName()
    {
        return 'toggle';
    }
}