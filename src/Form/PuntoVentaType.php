<?php

namespace App\Form;

use App\Entity\PuntoVenta;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class PuntoVentaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('documento',null,array('required'=>true))
            ->add('nombres',null,array('required'=>true))
            ->add('apellidos',null,array('required'=>true))
            ->add('email', EmailType::class, array('label' => 'Email'))
            ->add('celular',null,array('required'=>true))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PuntoVenta::class,
        ]);
    }
}
