<?php
declare(strict_types=1);

namespace App\Model\Work\Procedure\UseCase\Protocol\Requisite;


use App\ReadModel\Profile\Payment\Requisite\RequisiteFetcher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    /**
     * @var RequisiteFetcher
     */
    private $requisiteFetcher;

    /**
     * Form constructor.
     * @param RequisiteFetcher $requisiteFetcher
     */
    public function __construct(RequisiteFetcher $requisiteFetcher)
    {
        $this->requisiteFetcher = $requisiteFetcher;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $requisites = $this->requisiteFetcher->allArray($options['payment_id']);

        $builder
            ->add('requisite', Type\ChoiceType::class, [
                'choices' => array_flip($requisites)
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Command::class
        ]);

        $resolver->setRequired([
            'payment_id'
        ]);
    }
}