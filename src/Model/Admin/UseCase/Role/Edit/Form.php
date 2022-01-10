<?php
declare(strict_types=1);
namespace App\Model\Admin\UseCase\Role\Edit;


use App\Model\User\Entity\Profile\Role\Permission;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', Type\TextType::class)
            ->add('roleConstant', Type\TextType::class)
            ->add('permissions', Type\ChoiceType::class, [
                'choices' => array_combine(Permission::names(), Permission::names()),
                'required' => false,
                'expanded' => true,
                'multiple' => true,
                'translation_domain' => 'permissions',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => Command::class,
        ));
    }
}