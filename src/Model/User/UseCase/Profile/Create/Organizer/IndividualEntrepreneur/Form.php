<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Profile\Create\Organizer\IndividualEntrepreneur;

use App\ReadModel\Certificate\CertificateFetcher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    private $certificateFetcher;

    public function __construct(CertificateFetcher $certificateFetcher) {
        $this->certificateFetcher = $certificateFetcher;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $certificates = [];

        foreach ($this->certificateFetcher->activeCertificateListForUser($options['user_id']) as $certificate){
            $certificates[$certificate['name']] = $certificate['id'];
        }

        $builder->add('phone', Type\TextType::class)
            ->add('passportSeries', Type\TextType::class)
            ->add('passportNumber', Type\TextType::class)
            ->add('issuer', Type\TextType::class)
            ->add('ogrnip', Type\TextType::class)
            ->add('inn', Type\TextType::class, ['disabled' => true])
            ->add('position', Type\TextType::class)
            ->add('issuanceDate', Type\TextType::class)
            ->add('citizenship', Type\ChoiceType::class, [
                'choices' => [
                    'Российская Федерация' => 'RU'
                ]
            ])
            ->add('unitCode', Type\TextType::class)
            ->add('birthDay', Type\TextType::class)

            ->add('factCountry', Type\ChoiceType::class,[ 'label' => 'Страна', 'choices' =>[
                'Российская Федерация' => 'RU'
            ]])
            ->add('factRegion', Type\TextType::class)
            ->add('factCity', Type\TextType::class)
            ->add('factIndex', Type\TextType::class)
            ->add('factStreet', Type\TextType::class)
            ->add('factHouse', Type\TextType::class)

            ->add('legalCountry', Type\ChoiceType::class,[ 'label' => 'Страна', 'choices' =>[
                'Российская Федерация' => 'RU'
            ]])
            ->add('legalRegion', Type\TextType::class)
            ->add('legalCity', Type\TextType::class)
            ->add('legalIndex', Type\TextType::class)
            ->add('legalStreet', Type\TextType::class)
            ->add('legalHouse', Type\TextType::class)
            ->add('webSite', Type\TextType::class)
            ->add('certificate', Type\ChoiceType::class, ['choices' => $certificates]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Command::class
        ]);

        $resolver->setRequired(['user_id']);
    }
}