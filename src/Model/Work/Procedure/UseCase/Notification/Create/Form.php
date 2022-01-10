<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\UseCase\Notification\Create;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('notificationType', Type\ChoiceType::class, [
                'choices' => array_flip(\App\Model\Work\Procedure\Entity\XmlDocument\Type::$names)
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Command::class
        ]);
    }
}