<?php


namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Entity\FosUser;
use App\Entity\User;


class FosUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       $builder
            ->add('email', EmailType::class, array('label' => 'form.email', 'translation_domain' => 'FOSUserBundle'))
            ->add('username', null, array('label' => 'form.username', 'translation_domain' => 'FOSUserBundle'))

             ->add('documento',null,array('required'=>true))
             ->add('telefono',null,array('required'=>true,'label'=>'Teléfono'))
            
            ->add('roles', ChoiceType::class, [
                    'label'=>"",
                    'mapped' => false,
                    'required' => true,
                    'choices'  => [
                        'Selecciona un Rol' => '',
                        'Administrador' => 'ROLE_ADMIN',
                        'Administrador Gasolinera' => 'ROLE_GASOLINERA',
                        'Vendedor' => 'ROLE_VENDEDOR',
                    ]
                ]
            )


        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FosUser::class,
        ]);
    }
}
