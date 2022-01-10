<?php
declare(strict_types=1);

namespace App\Model\User\UseCase\Profile\Edit\AddBank;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('bankName', Type\TextType::class)
            ->add('paymentAccount', Type\TextType::class)
            ->add('correspondentAccount', Type\TextType::class)
            ->add('bankBik', Type\TextType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Command::class
        ]);
    }
}