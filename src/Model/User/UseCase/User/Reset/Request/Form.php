<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\User\Reset\Request;


use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class Form
 * @package App\Model\User\UseCase\User\Reset\Request
 */
class Form extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('email',Type\EmailType::class)
            ->add('captcha', Recaptcha3Type::class, [
                'constraints' => new Recaptcha3(['message' => '{{ errorCodes }}']),
                'action_name' => 'reset_password',
                'script_nonce_csp' => ''
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            'data_class' => Command::class,
        ]);
    }
}