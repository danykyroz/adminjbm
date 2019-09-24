<?php

namespace App\Form;

use App\Entity\Clientes;
use App\Entity\Delegacion;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Repository\DelegacionRepository;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;


class ClientesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('documento',null,array('required'=>true))
            ->add('nombres',null,array('required'=>true))
            ->add('apellidos',null,array('required'=>true))
            ->add('email', EmailType::class, array('label' => 'Email'))
            ->add('celular',null,array('required'=>true))
            ->add('avatar',FileType::class,array(
                            "label" => "Avatar",
                            "attr" =>array("class" => ""),
                             "data_class" => null,
                             "required"=>false,
            ))
            ->add('placa',null,array('required'=>true))
            ->add('delegacion', EntityType::class, [
                'class'         => Delegacion::class,
                'label'=>'Delegación',
                'query_builder' => function(EntityRepository $repo) {

                    return $repo->createQueryBuilder('d')->orderBy('d.municipio','Asc');
                }
            ])
            ->add('tipo', ChoiceType::class, [
                   
                    'mapped' => true,
                    'required' => true,
                    'choices'  => [
                        'Selecciona un Tipo' => '',
                        'Cliente Normal' => '1',
                        'Administrador Flotilla' => '2',
                    ]
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Clientes::class,
        ]);
    }
}
