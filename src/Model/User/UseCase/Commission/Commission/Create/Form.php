<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Commission\Commission\Create;


use App\Model\User\Entity\Commission\Commission\Status;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', Type\TextType::class)
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