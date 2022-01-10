<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\UseCase\Action\Open\Edit;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('procedureType', Type\ChoiceType::class, [
            'label' => 'Тип аукциона', 'choices' => [\App\Model\Work\Procedure\Entity\Type::auction()->getLocalizedName() => \App\Model\Work\Procedure\Entity\Type::auction()->getValue()]])
            ->add('pricePresentationForm', Type\ChoiceType::class, ['choices' => ['Открытая' => 'OPEN']])
            ->add('procedureName', Type\TextareaType::class)
            ->add('infoPointEntry', Type\TextareaType::class, ['disabled' => true])
            ->add('infoTradingVenue', Type\TextareaType::class, ['disabled' => true])
            ->add('infoBiddingProcess', Type\TextareaType::class);


    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Command::class
        ]);
    }
}