<?php

namespace App\Form;

use App\Entity\PuntoVenta;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PuntoVentaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('documento')
            ->add('nombres')
            ->add('apellidos')
            ->add('email')
            ->add('celular')
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PuntoVenta::class,
        ]);
    }
}
