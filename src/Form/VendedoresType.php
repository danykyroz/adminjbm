<?php

namespace App\Form;

use App\Entity\Vendedores;
use App\Entity\PuntosVenta;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class VendedoresType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('documento',null,array('required'=>true))
            ->add('nombres',null,array('required'=>true))
            ->add('apellidos',null,array('required'=>true))
            ->add('email', EmailType::class, array('label' => 'Email'))
            ->add('celular',null,array('required'=>true))
            ->add('puntoVenta', EntityType::class, [
                'class'         => PuntosVenta::class,
                'query_builder' => function(EntityRepository $repo) {

                    return $repo->createQueryBuilder('p')->orderBy('p.nombre','Asc');
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Vendedores::class,
        ]);
    }
}
