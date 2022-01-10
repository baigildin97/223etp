<?php
declare(strict_types=1);
namespace App\Model\Front\UseCase\Contacts;

use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type;

/**
 * Class Form
 * @package App\Model\Front\UseCase\Contacts
 */
class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', Type\TextType::class)
            ->add('email', Type\EmailType::class)
            ->add('phone', Type\TextType::class)
            ->add('text', Type\TextareaType::class)
            ->add('captcha', Recaptcha3Type::class, [
                'constraints' => new Recaptcha3(['message' => 'There were problems with your captcha. Please try again or contact with support and provide following code(s): {{ errorCodes }}']),
                'action_name' => 'contacts',
                'script_nonce_csp' => ''
            ]);
    }




    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => Command::class
        ));
    }
}