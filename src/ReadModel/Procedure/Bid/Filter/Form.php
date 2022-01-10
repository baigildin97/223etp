<?php
declare(strict_types=1);
namespace App\ReadModel\Procedure\Bid\Filter;


use App\Model\User\Entity\User\Role;
use App\Model\Work\Procedure\Entity\Lot\Bid\Status;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', Type\TextType::class, ['required' => false, 'attr' => [
                'placeholder' => 'Email',
                'onchange' => 'this.form.submit()',
            ]])
            ->add('status', Type\ChoiceType::class, ['choices' => [
                'Отправлен' => Status::sent()->getName(),
                'Отклонен' => Status::reject()->getName(),
                'Новый' => Status::new()->getName(),
                'Допущен' => Status::approved()->getName(),
                'Отозван' => Status::recalled()->getName()

            ], 'required' => false, 'placeholder' => 'Все статусы', 'attr' => ['onchange' => 'this.form.submit()']]
            )
            ->add('lotNumber', Type\TextType::class, ['required' => false, 'attr' => [
                'placeholder' => 'Номер лота',
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