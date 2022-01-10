<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Commission\Members\Edit;


use App\Model\User\Entity\Commission\Members\Status;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lastName', Type\TextType::class)
            ->add('firstName', Type\TextType::class)
            ->add('middleName', Type\TextType::class)
            ->add('position', Type\TextType::class)
            ->add('role', Type\TextType::class)
            ->add('status', Type\ChoiceType::class,[ 'label' => 'Статус', 'choices' =>[
                'Активный' => Status::active()->getValue(),
                'Черновик' => Status::draft()->getValue()
            ]]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Command::class
        ]);
    }
}