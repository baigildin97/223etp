<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Profile\ChangeCertificate\Individual;

use App\ReadModel\Certificate\CertificateFetcher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    private $certificateFetcher;

    public function __construct(CertificateFetcher $certificateFetcher){
        $this->certificateFetcher = $certificateFetcher;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $certificates = [];

        foreach ($this->certificateFetcher->activeCertificateListForUser($options['user_id']) as $certificate){
            $certificates[$certificate['name']] = $certificate['id'];
        }

        $builder->add('certificate', Type\ChoiceType::class, [
                'choices' => $certificates
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Command::class
        ]);

        $resolver->setRequired([
            'user_id'
        ]);
    }
}