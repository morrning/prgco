<?php
/**
 * Created by PhpStorm.
 * User: BABAK
 * Date: 11/24/2018
 * Time: 12:16 AM
 */

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class Imagebox extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {

    }

    public function getParent()
    {
        return FileType::class;
    }

    public function getName()
    {
        return 'Imagebox';
    }
}