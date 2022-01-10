<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\User\SignUp\Request;


use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
//            ->add('firstName', Role\TextType::class)
//            ->add('lastName', Role\TextType::class)
//            ->add('middleName', Role\TextType::class)
   //         ->add('phone', Type\TextType::class)
            ->add('email', Type\EmailType::class)
            ->add('plainPassword', Type\RepeatedType::class, [
                'type' => Type\PasswordType::class,
                'first_options' => ['label' => 'first_options'],
                'second_options' => ['label' => 'second_options']
            ])/*->add('captcha', Recaptcha3Type::class, [
                'constraints' => new Recaptcha3(['message' => '{{ errorCodes }}']),
                'action_name' => 'sign_up',
                'script_nonce_csp' => ''
            ])*/;
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Command::class,
        ]);
    }
}