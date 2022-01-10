<?php
declare(strict_types=1);

namespace App\Model\Admin\UseCase\Users\Edit;

use App\Model\User\Entity\User\Role;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('role', Type\ChoiceType::class, ['choices' => [
        'Пользователь' => Role::user()->getName(),
        'Модератор' => Role::moderator()->getName(),
        'Админ' => Role::admin()->getName()
        ]]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Command::class,
        ));
    }
}