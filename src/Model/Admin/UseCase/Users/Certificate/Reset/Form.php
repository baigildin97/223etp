<?php
namespace App\Model\Admin\UseCase\Users\Certificate\Reset;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type;


class Form extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('comment', Type\TextareaType::class, [
            'required' => false
        ])
            ->add('approved', Type\SubmitType::class)
            ->add('reject', Type\SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Command::class,
        ));
    }

}