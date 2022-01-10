<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\UseCase\Protocol\Create;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Model\Work\Procedure\Entity\Protocol\Type as ProtocolType;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('protocolType', Type\ChoiceType::class, [
            'choices' => array_flip(ProtocolType::$names)
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