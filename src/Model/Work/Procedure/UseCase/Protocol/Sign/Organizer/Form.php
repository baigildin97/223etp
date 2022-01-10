<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\UseCase\Protocol\Sign\Organizer;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('sign', Type\HiddenType::class)
            ->add('requisite', Type\HiddenType::class, ['data' => $options['requisite_id']]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Command::class
        ]);
        $resolver->setRequired(['requisite_id']);
    }
}