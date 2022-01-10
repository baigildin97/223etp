<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\UseCase\Lot\Open\Bid\Create;


use App\ReadModel\Profile\Payment\Requisite\Filter\Filter;
use App\ReadModel\Profile\Payment\Requisite\RequisiteFetcher;
use App\ReadModel\Profile\ProfileFetcher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type;

class Form extends AbstractType
{
    private const PER_PAGE = 10;

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

        $builder->add('requisite', Type\ChoiceType::class, [
                'label' => 'Реквизит',
                'choices' => array_flip($requisites)
            ]);
    }



    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Command::class
        ]);

        $resolver->setRequired(['user_id']);
    }

}
