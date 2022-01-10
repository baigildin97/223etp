<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\UseCase\Auction\Bet;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type;

class Form
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('sign', Type\HiddenType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Command::class
        ]);
    }
}
