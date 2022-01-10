<?php


namespace App\Model\Work\Procedure\UseCase\Lot\Create;


use App\Model\Work\Procedure\Entity\Lot\ArrestedPropertyType;
use App\Model\Work\Procedure\Entity\Lot\Nds;
use App\Model\Work\Procedure\Entity\Lot\Reload;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends \Symfony\Component\Form\AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('arrestedPropertyType', Type\ChoiceType::class, ['choices' => [
            (new ArrestedPropertyType(ArrestedPropertyType::IMMOVABLE_PROPERTY))->getLocalizedName() => ArrestedPropertyType::IMMOVABLE_PROPERTY,
            (new ArrestedPropertyType(ArrestedPropertyType::MOVABLE_PROPERTY))->getLocalizedName() => ArrestedPropertyType::MOVABLE_PROPERTY,
            (new ArrestedPropertyType(ArrestedPropertyType::PLEDGED_REAL_ESTATE))->getLocalizedName() => ArrestedPropertyType::PLEDGED_REAL_ESTATE,
            (new ArrestedPropertyType(ArrestedPropertyType::COLLATERALIZED_MOVABLE_PROPERTY))->getLocalizedName() => ArrestedPropertyType::COLLATERALIZED_MOVABLE_PROPERTY]])
            ->add('reloadLot', Type\ChoiceType::class, [
                'choices' => [
                    'Нет' => Reload::no()->getValue(),
                    'Да' => Reload::yes()->getValue()
                ],
                'expanded' => true,
                'multiple' => false,
                'label_attr' => ['class' => 'radio-inline']
            ])
            ->add('tenderBasic', Type\TextareaType::class)
            ->add('offerAuctionTime', Type\TextType::class, ['data' => 5, 'attr' => ['readonly' => true, 'value' => 5]])
            ->add('nds', Type\ChoiceType::class, ['choices' => [
                'НДС не облагается' => Nds::ndsIsExempt()->getName(),
                'В том числе НДС' => Nds::includingNds()->getName(),
                'Без НДС' => Nds::noNds()->getName()
            ],
                'expanded' => true,
                'multiple' => false,
                'label_attr' => ['class' => 'radio-inline']
            ])
            ->add('auctionStep', Type\MoneyType::class, [
                'divisor' => 100,
                'currency' => 'RUB'
            ])
            ->add('lotName', Type\TextareaType::class)
            ->add('startDateOfApplications', Type\TextType::class, ['attr' => ['placeholder' => 'дд.мм.гггг чч:мм']])
            ->add('closingDateOfApplications', Type\TextType::class, ['attr' => ['placeholder' => 'дд.мм.гггг чч:мм']])
            ->add('summingUpApplications', Type\TextType::class, ['attr' => ['placeholder' => 'дд.мм.гггг чч:мм']])
            ->add('startTradingDate', Type\TextType::class, ['attr' => ['placeholder' => 'дд.мм.гггг чч:мм']])
            ->add('debtorFullName', Type\TextType::class)
            ->add('debtorFullNameDateCase', Type\TextType::class)
            ->add('requisite', Type\TextareaType::class, ['attr' => ['placeholder' => 'Реквизиты для внесения задатка', 'rows' => 5]])
            ->add('depositPolicy', Type\TextareaType::class, ['attr' => ['rows' => 4]])
            ->add('region', Type\TextType::class)
            ->add('location_object', Type\TextType::class)
            ->add('pledgeer', Type\TextType::class, ['required' => false])
            // ->add('additional_object_characteristics', Type\TextareaType::class, ['required' => false])
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
    }
}