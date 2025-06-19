<?php

namespace App\Form;

use App\Entity\Task;
use App\Entity\Week;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WeekForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('month')
            ->add('year')
            ->add('debut')
            ->add('fin')
            ->add('task', EntityType::class, [
                'class' => Task::class,
                'choice_label' => 'id',
            ])
            ->add('tasks', EntityType::class, [
                'class' => Task::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Week::class,
        ]);
    }
}
