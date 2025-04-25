<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationFormType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, ['label' => "Nom d'utilisateur"])
            ->add('password', RepeatedType::class, [
                'type'            => PasswordType::class,
                'mapped'          => false,
                'invalid_message' => 'Les deux mots de passe doivent correspondre.',
                'required'        => $options['is_creation'],
                'first_options'   => ['label' => 'Mot de passe'],
                'second_options'  => ['label' => 'Tapez le mot de passe à nouveau'],
            ])
            ->add('email', EmailType::class, ['label' => 'Adresse email']);

        // Si on est en édition (is_creation=false), on expose le champ roles
        if (! $options['is_creation']) {
            $builder->add('roles', ChoiceType::class, [
                'label'    => 'Rôles',
                'choices'  => [
                    'Utilisateur'    => 'ROLE_USER',
                    'Administrateur' => 'ROLE_ADMIN',
                ],
                'expanded' => false,
                'multiple' => false,
                'attr'     => ['class' => 'form-select'],
            ]);

            $builder->get('roles')
                ->addModelTransformer(new CallbackTransformer(
                    // transform array (from DB) to single string for the form
                    fn ($rolesArray) => is_array($rolesArray) && count($rolesArray) ? $rolesArray[0] : null,
                    // transform the submitted string back to a one-element array
                    fn ($roleString) => [$roleString]
                ));
        }
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'  => User::class,
            'is_creation' => true,
        ]);
    }


}
