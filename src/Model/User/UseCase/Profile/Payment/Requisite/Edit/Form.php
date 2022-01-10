<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Profile\Payment\Requisite\Edit;

use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('bankName', Type\TextType::class)
            ->add('bankBik', Type\TextType::class)
            ->add('paymentAccount', Type\TextType::class)
            ->add('personalAccount', Type\TextType::class, ['required' => false])
            ->add('correspondentAccount', Type\TextType::class)
            ->add('bankAddress', Type\TextType::class, ['required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Command::class
        ]);
    }
}