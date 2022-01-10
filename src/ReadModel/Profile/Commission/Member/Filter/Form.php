<?php
declare(strict_types=1);
namespace App\ReadModel\Profile\Commission\Member\Filter;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type;


class Form extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('role', Type\TextType::class, ['required' => false, 'attr' => [
                'placeholder' => 'Роль',
                'onchange' => 'this.form.submit()',
            ]]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Filter::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }

}