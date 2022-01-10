<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Profile\Tariff\Create;


use App\Model\User\Entity\Profile\Tariff\Status;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('title', Type\TextType::class)
            ->add('cost', Type\MoneyType::class,[
                'divisor' => 100,
                'currency' => 'RUB'
            ])
            ->add('period', Type\IntegerType::class)
            ->add('status', Type\ChoiceType::class, ['label' => 'Статус', 'choices' =>[
                'Активный' => Status::active()->getName(),
                'Черновик' => Status::draft()->getName()
            ]])
            ->add('defaultPercent', Type\NumberType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Command::class
        ]);
    }
}