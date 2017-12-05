<?php

namespace FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints as Assert;
// on peut changer le nom d'une class, lui créé un alias qui sera valable que dans le fichier
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\OptionsResolver\OptionsResolver;


/**
 * Description of UserType
 *
 * @author Etudiant
 */
class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // méthode qu'il faut surcharger pour avoir nos différnets type données (crée une liste : username,etc..)
        
        global $app;
        
        $builder->add('username', TextType::class, [
            // ici, contraintes que l'on met sur un username
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\Length(['min' => 2, 'max' => 50]),
                new \Constraints\UniqueEntity([
                    'field' => 'username',
                    'dao' => $app['users.dao'],
                    'groups' => ['registration'] // Application : le groupe de validation est registration. Ce group est déclaré au moment du formulaire
                ])
            ],
            'label' => 'Nom d\'utilisateur'
        ])
        ->add('email', EmailType::class, [
            'constraints' => [
                 new Assert\NotBlank(),
                 new Assert\Email(),
                 new \Constraints\UniqueEntity([
                    'field' => 'email',
                    'dao' => $app['users.dao'],
                    'groups' => ['registration']
                 ])
            ],
            'label' => 'Adresse email'
        ])
        ->add('lastname', TextType::class, [
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\Length(['max' => 100])
            ],
            'label' => 'Nom'
        ])
        ->add('firstname', TextType::class, [
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\Length(['max' => 100])
            ],
            'label' => 'Prénom'
        ])
        ->add('password', RepeatedType::class, [
            'type' => PasswordType::class,
            'invalid_message' => 'The password field must match',
            'options' => ['attr' => ['class' => 'password-field']],
            'first_options' => [ // les contraintes à notre champ password normal
                'constraints' => [
                new Assert\NotBlank(),
                new Assert\Length(['min' => 5, 'max' => 30])
                ],
                'label' => 'Mot de passe'
            ],
            'second_options' => [
                'label' => 'Mot de passe de confirmation'
            ]
        ]) ;
        

    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        // configurer des options pour mon formulaire. par défaut userType associé à une Entity\User
        // $resolver contient plusieurs options mais juste une ici???
        $resolver->setDefaults([
            'data_class' => \Entity\User::class,
            'validation_groups' => ['edition']
        ]);
    }
   
}
