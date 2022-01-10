<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\UseCase\Action\Open\Create;


use App\Model\Work\Procedure\Entity\PriceForm;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type;


class Form extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder->add('procedureType', Type\ChoiceType::class, [
            'label' => 'Тип аукциона', 'choices' => [\App\Model\Work\Procedure\Entity\Type::auction()->getLocalizedName() => \App\Model\Work\Procedure\Entity\Type::auction()->getValue()]])
            ->add('organizerFullName', Type\TextType::class)
            ->add('organizerEmail', Type\EmailType::class)
            ->add('organizerPhone', Type\TextType::class)
            ->add('pricePresentationForm', Type\ChoiceType::class, ['choices' => ['Открытая' => PriceForm::OPEN]])
            ->add('procedureName', Type\TextareaType::class)
            ->add('infoPointEntry', Type\TextareaType::class, ['attr' => ['readonly' => true, 'rows' => 2]])
            ->add('infoTradingVenue', Type\TextareaType::class, ['attr' => ['readonly' => true, 'rows' => 2]])
            ->add('infoBiddingProcess', Type\TextareaType::class, ['attr' => ['rows' => 20]]);


    }

    public function configureOptions(OptionsResolver $resolver)
    {
       // $command = new Command();


        $resolver->setDefaults([
            'data_class' => Command::class
        ]);
    }
}
