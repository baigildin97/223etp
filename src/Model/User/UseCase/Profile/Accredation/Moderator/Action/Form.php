<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Profile\Accredation\Moderator\Action;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('cause', Type\TextareaType::class, [
            'required' => false
        ])
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