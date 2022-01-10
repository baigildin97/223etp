<?php


namespace App\Model\Work\Procedure\UseCase\Create;


use App\Model\Work\Procedure\Entity\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('procedureType', ChoiceType::class, ['choices' => [
            Type::auction()->getLocalizedName() => Type::AUCTION,
            Type::contest()->getLocalizedName() => Type::CONTEST,
            Type::requestOffers()->getLocalizedName() => Type::REQUEST_OFFERS
        ]]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
    }
}