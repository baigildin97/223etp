<?php
declare(strict_types=1);
namespace App\ReadModel\Procedure\Filter;

use App\Model\Work\Procedure\Entity\Lot\Reload;
use App\Model\Work\Procedure\Entity\Status;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('id_number', Type\TextType::class, ['required' => false, 'attr' => [
                'placeholder' => 'ID',
                'onchange' => 'this.form.submit()',
            ]])
            ->add('statusFilter', Type\ChoiceType::class, ['choices' => [
                'Активный' => Status::active()->getName(),
                'Несостоявшийся' => Status::failed()->getName(),
                'Идёт прием заявок' => Status::acceptingApplications()->getName(),
                'Окончен прием заявок' => Status::applicationsReceived()->getName(),
                'Подведение итогов приема заявок' => Status::statusSummingUpApplications()->getName(),
                'Ожидает началы торгов' => Status::statusStartOfTrading()->getName(),
                'Идут торги' => Status::statusBiddingProcess()->getName(),
                'Торги завершены' => Status::statusBiddingCompleted()->getName()
            ], 'required' => false, 'placeholder' => 'Все статусы', 'attr' => ['onchange' => 'this.form.submit()']])
            ->add('nameOrgInn', Type\TextType::class, ['required' => false, 'attr' => [
                'placeholder' => 'Название организации или ИНН',
                'onchange' => 'this.form.submit()',
            ]])
             ->add('title', Type\TextType::class, ['required' => false, 'attr' => [
                'placeholder' => 'Наименование',
                'onchange' => 'this.form.submit()',
            ]]);

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
