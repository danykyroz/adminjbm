<?php

namespace App\Form;

use App\Entity\Gasolineras;
use App\Entity\Delegacion;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Repository\DelegacionRepository;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GasolinerasType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre',null,array('required'=>true))
            ->add('delegacion', EntityType::class, [
                'class'         => Delegacion::class,
                'query_builder' => function(EntityRepository $repo) {

                    return $repo->createQueryBuilder('d')->orderBy('d.municipio','Asc');
                }
            ])
            ->add('direccion',null,array('required'=>true))
            ->add('codigoPostal')
            ->add('nombreEncargado',null,array('required'=>true))
            ->add('telefonoEncargado',null,array('required'=>true))
            
        ;   
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Gasolineras::class,
        ]);
    }
}
