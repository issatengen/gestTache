<?php

namespace App\Form;

use App\Entity\Role;
use App\Entity\Department;
use App\Entity\Task;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FileType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('name')
            ->add('surname')
            ->add('telephone')
            ->add('department', EntityType::class, [
                'class' => Department::class,
                'placeholder' => 'Selectionner un département',
                'choice_label' => 'label',
            ])
            // ->add('department')
            // ->add('tasks', EntityType::class, [
            //     'class' => Task::class,
            //     'choice_label' => 'id',
            //     'multiple' => true,
            // ])
            ->add('role', EntityType::class, [
                'class' => Role::class,
                'choice_label' => 'code',
                'placeholder' => 'Selectionner un rôle',
            ])
            ->add('profile_picture', \Symfony\Component\Form\Extension\Core\Type\FileType::class, [
                'label' => 'Image (PNG, JPG)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new \Symfony\Component\Validator\Constraints\File([
                        'maxSize' => '3072k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image (PNG or JPG)',
                    ]),
                ],
            ]);
            if($options['include_password']) {
                $builder->add('password');
            }
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'include_password' => false,
        ]);
    }
}
