<?php
/**
 * Created by PhpStorm.
 * User: babak
 * Date: 13/06/2018
 * Time: 12:21 PM
 */

namespace App\Form\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class UsernameType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {

    }

    public function getParent()
    {
        return EntityType::class;
    }

    public function getName()
    {
        return 'baseUserList';
    }
}