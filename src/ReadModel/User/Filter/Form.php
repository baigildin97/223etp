<?php
declare(strict_types=1);
namespace App\ReadModel\User\Filter;

use App\Model\User\Entity\Profile\Organization\IncorporationForm;
use App\Model\User\Entity\Profile\Status;
use App\Model\User\Entity\User\Role;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', Type\TextType::class, ['required' => false, 'attr' => [
                'placeholder' => 'Email',
                'onchange' => 'this.form.submit()',
            ]])
            ->add('userName', Type\TextType::class, ['required' => false, 'attr' => [
                'placeholder' => 'Имя пользователя',
                'onchange' => 'this.form.submit()',
            ]])
            ->add('phone', Type\TextType::class, ['required' => false, 'attr' => [
                'placeholder' => 'Телефон',
                'onchange' => 'this.form.submit()',
            ]])
            ->add('inn', Type\TextType::class, ['required' => false, 'attr' => [
                'placeholder' => 'ИНН',
                'onchange' => 'this.form.submit()',
            ]])
            ->add('clientIp', Type\TextType::class, ['required' => false, 'attr' => [
                'placeholder' => 'IP'
            ]])
            ->add('incorporationForm', Type\ChoiceType::class, ['choices' => [
                'Физическое лицо' => IncorporationForm::individual()->getName(),
                'Индивидуальный предприниматель' => IncorporationForm::individualEntrepreneur()->getName(),
                'Юридическое лицо' => IncorporationForm::legalEntity()->getName()
            ], 'required' => false, 'placeholder' => 'Тип пользователя', 'attr' => ['onchange' => 'this.form.submit()']])

            ->add('status', Type\ChoiceType::class, ['choices' => [
                'Активный' => Status::active()->getName(),
                'Черновик' => Status::wait()->getName(),
                'На модерации' => Status::moderate()->getName(),
                'Отклонен' => Status::rejected()->getName(),
                'Заблокированный' => Status::blocked()->getName(),
            ], 'required' => false, 'placeholder' => 'Все статусы', 'attr' => ['onchange' => 'this.form.submit()']])
            ->add('role', Type\ChoiceType::class, ['choices' => [
                'Пользователь' => Role::USER,
                'Админ' => Role::ADMIN,
                'Модератор' => Role::MODERATOR,
            ], 'required' => false, 'placeholder' => 'Все роли', 'attr' => ['onchange' => 'this.form.submit()']]);
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Filter::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }
}
