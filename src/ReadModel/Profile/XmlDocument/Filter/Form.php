<?php
declare(strict_types=1);
namespace App\ReadModel\Profile\XmlDocument\Filter;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('userName', Type\TextType::class, ['required' => false, 'attr' => [
                'placeholder' => 'Имя пользователя'
            ]])
            ->add('subjectNameInn', Type\TextType::class, ['required' => false, 'attr' => [
                'placeholder' => 'ИНН'
            ]])
            ->add('email', Type\TextType::class, ['required' => false, 'attr' => [
                'placeholder' => 'Email'
            ]])
            ->add('phone', Type\TextType::class, ['required' => false, 'attr' => [
                'placeholder' => 'Телефон'
            ]])
            ->add('clientIp', Type\TextType::class, ['required' => false, 'attr' => [
                'placeholder' => 'IP'
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