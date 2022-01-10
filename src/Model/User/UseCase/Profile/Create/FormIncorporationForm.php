<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Profile\Create;


use App\Model\User\Entity\Profile\Organization\IncorporationForm;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormIncorporationForm extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $arr = array_flip([
            'Юридическое лицо' => IncorporationForm::legalEntity()->getName(),
            'Индивидуальный предприниматель' => IncorporationForm::individualEntrepreneur()->getName(),
            'Физическое лицо' => IncorporationForm::individual()->getName(),
        ]);


        unset($arr[$options['userType']]);
        $arr2 = array_flip($arr);

        $builder->add('formOfIncorporation', ChoiceType::class,[
            'label' => 'Организационно-правовая форма',
            'choices' => $arr2]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CommandIncorporationForm::class
        ]);

        $resolver->setRequired([
            'userType'
        ]);
    }


}