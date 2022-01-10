<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Profile\Create;


use App\Model\User\Entity\Profile\Role;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormTypeProfile extends AbstractType
{


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('typeProfile', ChoiceType::class,[ 'label' => '', 'choices' =>[
            'Регистрация в качестве организатора' =>'ROLE_ORGANIZER',
            'Регистрация в качестве участника' => 'ROLE_PARTICIPANT',
        ]]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CommandTypeForm::class
        ]);
    }
}