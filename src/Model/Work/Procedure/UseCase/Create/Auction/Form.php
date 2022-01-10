<?php
declare(strict_types=1);

namespace App\Model\Work\Procedure\UseCase\Create\Auction;


use App\Model\Work\Procedure\Entity\ConductingType;
use App\Model\Work\Procedure\Entity\Lot\ArrestedPropertyType;
use App\Model\Work\Procedure\Entity\Lot\Nds;
use App\Model\Work\Procedure\Entity\Lot\Reload;
use App\Model\Work\Procedure\Entity\PriceForm;
use App\ReadModel\Procedure\ProcedureFetcher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type;


class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('procedureType', Type\ChoiceType::class, [
            'label' => 'Форма проведения', 'choices' => [
                ConductingType::open()->getLocalizedName() => ConductingType::OPEN,
                ConductingType::closed()->getLocalizedName() => ConductingType::CLOSED
            ]])
            ->add('organizerFullName', Type\TextType::class)
            ->add('organizerEmail', Type\EmailType::class)
            ->add('organizerPhone', Type\TextType::class)
            ->add('pricePresentationForm', Type\ChoiceType::class, ['choices' => [
                PriceForm::open()->getLocalizedName() => PriceForm::OPEN,
                PriceForm::close()->getLocalizedName() => PriceForm::CLOSE
            ]])
            ->add('procedureName', Type\TextType::class)
            ->add('infoPointEntry', Type\TextareaType::class, ['attr' => ['readonly' => true, 'rows' => 2]])
            ->add('infoTradingVenue', Type\TextareaType::class, ['attr' => ['readonly' => true, 'rows' => 2]])
            ->add('infoBiddingProcess', Type\TextareaType::class, ['attr' => ['rows' => 20]])
            ->add('tenderingProcess', Type\TextareaType::class, ['attr' => ['rows' => 14]]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Command::class
        ]);
    }
}