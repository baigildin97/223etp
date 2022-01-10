<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Profile\Tariff\Levels\Create;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('amount', Type\MoneyType::class,[
            'divisor' => 100,
            'currency' => 'RUB'])
            ->add('priority', Type\NumberType::class)
            ->add('percent', Type\NumberType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Command::class
        ]);
    }
}