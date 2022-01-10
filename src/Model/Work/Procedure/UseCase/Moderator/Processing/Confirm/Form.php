<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\UseCase\Moderator\Processing\Confirm;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\OptionsResolver\OptionsResolver;


class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cause', Type\TextareaType::class, ['required'   => false])
            ->add('approved', Type\SubmitType::class)
            ->add('reject', Type\SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Command::class
        ]);
    }
}