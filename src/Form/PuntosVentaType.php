<?php

namespace App\Form;

use App\Entity\PuntosVenta;
use App\Entity\Gasolineras;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PuntosVentaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
         $builder
            ->add('nombre',null,array('required'=>true))
            ->add('gasolinera', EntityType::class, [
                'class'         => Gasolineras::class,
                'query_builder' => function(EntityRepository $repo) {

                    return $repo->createQueryBuilder('g')->orderBy('g.nombre','Asc');
                }
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PuntosVenta::class,
        ]);
    }
}
