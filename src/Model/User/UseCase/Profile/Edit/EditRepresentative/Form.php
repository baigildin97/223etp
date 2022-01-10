<?php
declare(strict_types=1);

namespace App\Model\User\UseCase\Profile\Edit\EditRepresentative;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $currentYear = (new \DateTimeImmutable())->format('Y');

        $builder->add('position', Type\TextType::class)
            ->add('confirmingDocument', Type\TextType::class)
            ->add('phone', Type\TextType::class)
            ->add('email', Type\EmailType::class)
            ->add('passportSeries', Type\TextType::class)
            ->add('passportNumber', Type\TextType::class)
            ->add('passportIssuer', Type\TextType::class)
            ->add('unitCode', Type\TextType::class)
            ->add('citizenship', Type\TextType::class)
            ->add('passportIssuanceDate', Type\DateType::class,
                ['format' => Type\DateType::DEFAULT_FORMAT,
                 'years' => range($currentYear - 80, $currentYear, 1)])
            ->add('birthDate', Type\DateType::class,
                ['format' => Type\DateType::DEFAULT_FORMAT,
                 'years' => range($currentYear - 80, $currentYear - 17, 1)]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Command::class
        ]);
    }
}