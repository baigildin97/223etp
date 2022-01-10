<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\UseCase\Edit;


use App\Model\Work\Procedure\Entity\Lot\ArrestedPropertyType;
use App\Model\Work\Procedure\Entity\Lot\Nds;
use App\Model\Work\Procedure\Entity\Lot\Reload;
use App\Model\Work\Procedure\Entity\PriceForm;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type;

class Form extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('procedureType', Type\ChoiceType::class, [
            'label' => 'Тип аукциона',
            'choices' => [\App\Model\Work\Procedure\Entity\Type::auction()->getLocalizedName() => \App\Model\Work\Procedure\Entity\Type::auction()->getValue()],
            'attr' => ['readonly' => true]
        ])
            ->add('organizerFullName', Type\TextType::class)
            ->add('organizerEmail', Type\EmailType::class)
            ->add('organizerPhone', Type\TextType::class)
            ->add('pricePresentationForm', Type\ChoiceType::class, ['choices' => ['Открытая' => PriceForm::OPEN]])
            ->add('procedureName', Type\TextareaType::class)
            ->add('infoPointEntry', Type\TextareaType::class, ['attr' => ['readonly' => true, 'rows' => 2]])
            ->add('infoTradingVenue', Type\TextareaType::class, ['attr' => ['readonly' => true, 'rows' => 2]])
            ->add('infoBiddingProcess', Type\TextareaType::class, ['attr' => ['rows' => 20]])
            ->add('arrestedPropertyType', Type\ChoiceType::class, ['choices' => [
                (new ArrestedPropertyType(ArrestedPropertyType::IMMOVABLE_PROPERTY))->getLocalizedName() => ArrestedPropertyType::IMMOVABLE_PROPERTY,
                (new ArrestedPropertyType(ArrestedPropertyType::MOVABLE_PROPERTY))->getLocalizedName() => ArrestedPropertyType::MOVABLE_PROPERTY,
                (new ArrestedPropertyType(ArrestedPropertyType::PLEDGED_REAL_ESTATE))->getLocalizedName() => ArrestedPropertyType::PLEDGED_REAL_ESTATE,
                (new ArrestedPropertyType(ArrestedPropertyType::COLLATERALIZED_MOVABLE_PROPERTY))->getLocalizedName() => ArrestedPropertyType::COLLATERALIZED_MOVABLE_PROPERTY]])
            ->add('reloadLot', Type\ChoiceType::class, ['choices' => ['Нет' => Reload::no()->getValue(), 'Да' => Reload::yes()->getValue()]])
            ->add('tenderBasic', Type\TextareaType::class)
            ->add('offerAuctionTime', Type\TextType::class, ['data' => 5, 'attr' => ['readonly' => true, 'value' => 5]])
            ->add('nds', Type\ChoiceType::class, ['choices' => [
                'НДС не облагается' => Nds::ndsIsExempt()->getName(),
                'В том числе НДС' => Nds::includingNds()->getName(),
                'Без НДС' => Nds::noNds()->getName()
            ]])
            ->add('auctionStep', Type\MoneyType::class, [
                'divisor' => 100,
                'currency' => 'RUB'
            ])
            ->add('startDateOfApplications', Type\TextType::class, ['attr' => ['placeholder' => 'дд.мм.гггг чч:мм']])
            ->add('closingDateOfApplications', Type\TextType::class, ['attr' => ['placeholder' => 'дд.мм.гггг чч:мм']])
            ->add('summingUpApplications', Type\TextType::class, ['attr' => ['placeholder' => 'дд.мм.гггг чч:мм']])
            ->add('startTradingDate', Type\TextType::class, ['attr' => ['placeholder' => 'дд.мм.гггг чч:мм']])
            ->add('debtorFullName', Type\TextType::class)
            ->add('debtorFullNameDateCase', Type\TextType::class)
            ->add('requisite', Type\TextareaType::class, ['attr' => ['placeholder' => 'Реквизиты для внесения задатка', 'rows' => 5]])
            ->add('region', Type\TextType::class)
            ->add('depositPolicy', Type\TextareaType::class,['attr' => ['rows' => 4]])
            ->add('tenderingProcess', Type\TextareaType::class,['attr' => ['rows' => 14]])
            ->add('location_object', Type\TextType::class)
            ->add('pledgeer', Type\TextType::class, ['required' => false])
          //  ->add('additional_object_characteristics', Type\TextareaType::class, ['required' => false])
            ->add('starting_price', Type\MoneyType::class, [
                'divisor' => 100,
                'currency' => 'RUB'
            ])
            ->add('advancePaymentTime', Type\TextType::class, ['attr' => ['placeholder' => 'дд.мм.гггг чч:мм']])
            ->add('deposit_amount', Type\MoneyType::class, [
                'divisor' => 100,
                'currency' => 'RUB'
            ])
            ->add('bailiffsName', Type\TextType::class)
            ->add('bailiffsNameDativeCase', Type\TextType::class)
            ->add('executiveProductionNumber', Type\TextType::class)
            ->add('dateEnforcementProceedings', Type\TextType::class);


    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Command::class
        ]);
        $resolver->setRequired(['user_id']);
    }
}