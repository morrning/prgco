<?php
/**
 * Created by PhpStorm.
 * User: ICT
 * Date: 6/28/2018
 * Time: 5:59 PM
 */

namespace App\Form\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class FileboxType extends AbstractType
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
        return 'Filebox';
    }
}