<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Profile\Payment\Transaction\Deposit;


use App\ReadModel\Profile\Payment\Requisite\RequisiteFetcher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type;

class Form extends AbstractType
{

    private $requisiteFetcher;

    public function __construct(RequisiteFetcher $requisiteFetcher) {
        $this->requisiteFetcher = $requisiteFetcher;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = [];
        foreach ($this->requisiteFetcher->findAllActiveSelectOptionsList($options['payment_id']) as $requisite){
            $choices[$requisite['bank_name'].' - Р.Сч:'.$requisite['payment_account']] = $requisite['id'];
        }

        $builder
            ->add('requisiteId', Type\ChoiceType::class, ['choices' => $choices])
            ->add('money', Type\MoneyType::class, [
            'divisor' => 100,
            'currency' => 'RUB'
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Command::class
        ]);
        $resolver->setRequired(['payment_id']);
    }

}