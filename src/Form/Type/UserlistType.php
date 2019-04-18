<?php
/**
 * Created by PhpStorm.
 * User: babak
 * Date: 26/05/2018
 * Time: 05:47 AM
 */

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class UserlistType extends AbstractType
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
        return 'userList';
    }

}