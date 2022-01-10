<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Profile\Tariff\Edit;


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
            ->add('cost', Type\TextType::class)
            ->add('period', Type\IntegerType::class)
            ->add('status', Type\ChoiceType::class, [ 'label' => 'Статус', 'choices' =>[
                'Активный' => Status::active()->getName(),
                'Черновик' => Status::draft()->getName()
            ]]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Command::class
        ]);
    }
}