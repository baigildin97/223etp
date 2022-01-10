<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\UseCase\Lot\Open\Edit;


use App\Model\Work\Procedure\Entity\Lot\Reload;
use App\ReadModel\Profile\Payment\Requisite\RequisiteFetcher;
use App\ReadModel\Profile\ProfileFetcher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class Form extends AbstractType
{
    private $requisiteFetcher;
    private $profileFetcher;

    public function __construct(RequisiteFetcher $requisiteFetcher, ProfileFetcher $profileFetcher){
        $this->requisiteFetcher = $requisiteFetcher;
        $this->profileFetcher = $profileFetcher;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $profile = $this->profileFetcher->findDetailByUserId($options['user_id']);

        $requisites = $this->requisiteFetcher->allArray($profile->payment_id);

        $builder->add('arrestedPropertyType', Type\ChoiceType::class, ['choices' => [
            'Недвижимое имущество' => 'IMMOVABLE_PROPERTY',
            'Движимое имущество' => 'MOVABLE_PROPERTY',
            'Залоговое недвижимое имущество' => 'PLEDGED_REAL_ESTATE',
            'Залоговое движимое имущество' => 'COLLATERALIZED_MOVABLE_PROPERTY']])
            ->add('idNumber', Type\IntegerType::class)
            ->add('reloadLot', Type\ChoiceType::class, [ 'choices' => [
                        'Нет' => Reload::no()->getValue(),
                        'Да' => Reload::yes()->getValue()
            ]])
            ->add('tenderBasic', Type\TextareaType::class)
            ->add('offerAuctionTime', Type\TextType::class, ['data' => 5, 'attr' =>['readonly' => true, 'value' => 5]])
            ->add('nds', Type\ChoiceType::class, ['choices' => [
                'НДС не облагается' => 'NDS_IS_EXEMPT',
                'В том числе НДС' => 'INCLUDING_NDS',
                'Без НДС' => 'NO_NDS']])
            ->add('auctionStep', Type\MoneyType::class, [
                'divisor' => 100,
                'currency' => 'RUB'
            ])
            ->add('startDateOfApplications', Type\TextType::class, ['attr' =>[ 'placeholder' => 'дд.мм.гггг чч:мм']])
            ->add('closingDateOfApplications', Type\TextType::class, ['attr' =>[ 'placeholder' => 'дд.мм.гггг чч:мм']])
            ->add('summingUpApplications', Type\TextType::class, ['attr' =>[ 'placeholder' => 'дд.мм.гггг чч:мм']])
            ->add('startTradingDate', Type\TextType::class, ['attr' =>[ 'placeholder' => 'дд.мм.гггг чч:мм']])

            ->add('debtorFullName', Type\TextType::class)
            ->add('debtorFullNameDateCase', Type\TextType::class)
            ->add('requisite', Type\TextareaType::class, ['attr' => ['placeholder' => 'Реквизиты для внесения задатка', 'rows' => 5]])

            ->add('lotName', Type\TextareaType::class)
            ->add('region', Type\TextType::class)

            ->add('location_object', Type\TextType::class)
            ->add('pledgeer', Type\TextType::class, ['required' => false])


            ->add('additional_object_characteristics', Type\TextareaType::class, ['required' => false])
            ->add('starting_price', Type\MoneyType::class, [
                'divisor' => 100,
                'currency' => 'RUB'
            ])
            ->add('advancePaymentTime', Type\TextType::class, ['attr' =>[ 'placeholder' => 'дд.мм.гггг чч:мм']])
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