<?php

namespace App\Form;

use App\Entity\Role;
use App\Entity\Task;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
            // ->add('tasks', EntityType::class, [
            //     'class' => Task::class,
            //     'choice_label' => 'id',
            //     'multiple' => true,
            // ])
            ->add('role', EntityType::class, [
                'class' => Role::class,
                'choice_label' => 'code',
                'placeholder' => 'Select a role',
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
