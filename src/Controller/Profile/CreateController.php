<?php
declare(strict_types=1);

namespace App\Controller\Profile;


use App\Model\User\Entity\Certificate\Status;
use App\Model\User\Entity\Profile\Organization\IncorporationForm;
use App\Model\User\Entity\Profile\Role\Role;
use App\Model\User\UseCase\Profile\Create\Organizer\LegalEntity\Handler as LegalEntityOrganizerHandler;
use App\Model\User\UseCase\Profile\Create\Organizer\LegalEntity\Command as LegalEntityOrganizerCommand;
use App\Model\User\UseCase\Profile\Create\Organizer\LegalEntity\Form as LegalEntityOrganizerForm;

use App\Model\User\UseCase\Profile\Create\Participant\LegalEntity\Handler as LegalEntityParticipantHandler;
use App\Model\User\UseCase\Profile\Create\Participant\LegalEntity\Command as LegalEntityParticipantCommand;
use App\Model\User\UseCase\Profile\Create\Participant\LegalEntity\Form as LegalEntityParticipantForm;

use App\Model\User\UseCase\Profile\Create\Organizer\IndividualEntrepreneur\Handler as OrganizerIndividualEntrepreneurHandler;
use App\Model\User\UseCase\Profile\Create\Organizer\IndividualEntrepreneur\Command as OrganizerIndividualEntrepreneurCommand;
use App\Model\User\UseCase\Profile\Create\Organizer\IndividualEntrepreneur\Form as OrganizerIndividualEntrepreneurForm;

use App\Model\User\UseCase\Profile\Create\Participant\IndividualEntrepreneur\Handler as ParticipantIndividualEntrepreneurHandler;
use App\Model\User\UseCase\Profile\Create\Participant\IndividualEntrepreneur\Command as ParticipantIndividualEntrepreneurCommand;
use App\Model\User\UseCase\Profile\Create\Participant\IndividualEntrepreneur\Form as ParticipantIndividualEntrepreneurForm;

use App\Model\User\UseCase\Profile\Create\Participant\Individual\Handler as IndividualParticipantHandler;
use App\Model\User\UseCase\Profile\Create\Participant\Individual\Command as IndividualParticipantCommand;
use App\Model\User\UseCase\Profile\Create\Participant\Individual\Form as IndividualParticipantForm;

use App\Model\User\UseCase\Profile\Create;
use App\ReadModel\Certificate\CertificateFetcher;
use App\ReadModel\Certificate\Filter\Filter;
use App\ReadModel\Profile\ProfileFetcher;
use App\ReadModel\User\UserFetcher;
use App\Security\Voter\ProfileAccess;
use App\Services\Uploader\FileUploader;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class CreateController
 * @package App\Controller\Profile
 * @Route("/profile/create", name="profile.create")
 */
class CreateController extends AbstractController
{

    private $translator;
    private $logger;

    public function __construct(TranslatorInterface $translator, LoggerInterface $logger)
    {
        $this->translator = $translator;
        $this->logger = $logger;
    }



    /**
     * @param Request $request
     * @param CertificateFetcher $certificateFetcher
     * @return Response
     * @Route("", name="")
     */
    public function start(Request $request, CertificateFetcher $certificateFetcher, UserFetcher $userFetcher): Response
    {

        if (!$certificateFetcher->existsByStatusCertificates(Status::active(), Filter::forUserId($user_id = $this->getUser()->getId()))) {
            $this->addFlash('success', $this->translator->trans('Add your certificate.', [], 'exceptions'));
            return $this->redirectToRoute('certificates', ['user_id' => $user_id]);
        }

        if ($userFetcher->findDetail($user_id)->issetActiveProfile()) {
            throw new \DomainException("Page Not Found", 404);
        }


        $form = $this->createForm(Create\FormTypeProfile::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (Role::ROLE_PARTICIPANT === $form->getData()->typeProfile) {
                return $this->redirectToRoute('profile.create.participant');
            }
            if (Role::ROLE_ORGANIZER === $form->getData()->typeProfile) {
                return $this->redirectToRoute('profile.create.organizer');
            }
        }

        return $this->render('app/profile/create/start.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * @param Request $request
     * @return Response
     * @Route("/organizer", name=".organizer")
     */
    public function organizer(Request $request): Response
    {
        $form = $this->createForm(Create\FormIncorporationForm::class, new Create\CommandIncorporationForm(), ['userType' => IncorporationForm::individual()->getName()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $incorporationForm = new IncorporationForm($form->getData()->formOfIncorporation);
            if ($incorporationForm->isLegalEntity()) {
                return $this->redirectToRoute('profile.create.organizer.legal_entity');
            }
            if ($incorporationForm->isIndividualEntrepreneur()) {
                return $this->redirectToRoute('profile.create.organizer.individual_entrepreneur');
            }
        }

        return $this->render('app/profile/create/type_profile.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * @param Request $request
     * @return Response
     * @Route("/participant", name=".participant")
     */
    public function participant(Request $request): Response
    {
        $form = $this->createForm(Create\FormIncorporationForm::class, new Create\CommandIncorporationForm(), ['userType' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $incorporationForm = new IncorporationForm($form->getData()->formOfIncorporation);
            if ($incorporationForm->isLegalEntity()) {
                return $this->redirectToRoute('profile.create.participant.legal_entity');
            }
            if ($incorporationForm->isIndividualEntrepreneur()) {
                return $this->redirectToRoute('profile.create.participant.individual_entrepreneur');
            }
            if ($incorporationForm->isIndividual()) {
                return $this->redirectToRoute('profile.create.participant.individual');
            }
        }

        return $this->render('app/profile/create/type_profile.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * Организатор юр лицо
     * @param Request $request
     * @param Create\Organizer\LegalEntity\Handler $handler
     * @param CertificateFetcher $certificateFetcher
     * @return Response
     * @Route("/organizer/legal-entity", name=".organizer.legal_entity")
     */
    public function createOrganizerLegalEntity(Request $request, LegalEntityOrganizerHandler $handler, CertificateFetcher $certificateFetcher): Response
    {
        $command = new LegalEntityOrganizerCommand($userId = $this->getUser()->getId(), $request->getClientIp());
        $form = $this->createForm(LegalEntityOrganizerForm::class, $command, ['user_id' => $userId]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', $this->translator->trans('Profile added successfully.', [], 'exceptions'));
                return $this->redirectToRoute('home');
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
            }
        }

        return $this->render('app/profile/create/organizer/legal_entity.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * Организатор индивидуальный предприниматель
     * @param Request $request
     * @param OrganizerIndividualEntrepreneurHandler $handler
     * @return Response
     * @Route("/organizer/individual-entrepreneur/", name=".organizer.individual_entrepreneur", )
     */
    public function createOrganizerIndividualEntrepreneur(Request $request, OrganizerIndividualEntrepreneurHandler $handler): Response
    {
        $command = new OrganizerIndividualEntrepreneurCommand($user_id = $this->getUser()->getId(), $request->getClientIp());
        $form = $this->createForm(OrganizerIndividualEntrepreneurForm::class, $command, ['user_id' => $user_id]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', $this->translator->trans('Profile added successfully.', [], 'exceptions'));
                return $this->redirectToRoute('home');
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
            }
        }
        return $this->render('app/profile/create/organizer/individual_entrepreneur.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Участник индивидуальный предприниматель
     * @param Request $request
     * @param ParticipantIndividualEntrepreneurHandler $handler
     * @return Response
     * @Route("/participant/individual-entrepreneur/", name=".participant.individual_entrepreneur", )
     */
    public function createParticipantIndividualEntrepreneur(Request $request, ParticipantIndividualEntrepreneurHandler $handler): Response
    {
        $command = new ParticipantIndividualEntrepreneurCommand($user_id = $this->getUser()->getId(), $request->getClientIp());
        $form = $this->createForm(ParticipantIndividualEntrepreneurForm::class, $command, ['user_id' => $user_id]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', $this->translator->trans('Profile added successfully.', [], 'exceptions'));
                return $this->redirectToRoute('home');
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
            }
        }
        return $this->render('app/profile/create/participant/individual_entrepreneur.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Участник юр лицо
     * @param Request $request
     * @param LegalEntityParticipantHandler $handler
     * @param FileUploader $fileUploader
     * @return Response
     * @Route("/participant/legal-entity", name=".participant.legal_entity")
     */
    public function createParticipantLegalEntity(Request $request, LegalEntityParticipantHandler $handler, FileUploader $fileUploader): Response
    {
        $command = new LegalEntityParticipantCommand($userId = $this->getUser()->getId(), $request->getClientIp());
        $form = $this->createForm(LegalEntityParticipantForm::class, $command, ['user_id' => $userId]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', $this->translator->trans('Profile added successfully.', [], 'exceptions'));
                return $this->redirectToRoute('home');
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
            }
        }

        return $this->render('app/profile/create/participant/legal_entity.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * Участник физическое лицо
     * @param Request $request
     * @param IndividualParticipantHandler $handler
     * @param FileUploader $fileUploader
     * @return Response
     * @Route("/participant/individual", name=".participant.individual")
     */
    public function createParticipantIndividual(Request $request, IndividualParticipantHandler $handler, FileUploader $fileUploader): Response
    {
        $command = new IndividualParticipantCommand($user_id = $this->getUser()->getId(), $request->getClientIp());
        $form = $this->createForm(IndividualParticipantForm::class, $command, ['user_id' => $user_id]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', $this->translator->trans('Profile added successfully.', [], 'exceptions'));
                return $this->redirectToRoute('home');
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
            }
        }

        return $this->render('app/profile/create/participant/individual.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Возвращает информацию об сертификате
     * @param Request $request
     * @param string $id
     * @param CertificateFetcher $certificateFetcher
     * @return JsonResponse
     * @Route("/getinfocertificate/{id}", name="get.info.certificate.ajax")
     */
    public function ajaxGetInfoCertificate(Request $request, string $id, CertificateFetcher $certificateFetcher): Response{
        if ($request->isXmlHttpRequest()) {
            $certificate = $certificateFetcher->findDetail($id);

            $inn = $certificate->subject_name_inn;
            $snils = $certificate->subject_name_snils;

            if ($certificate->isLegalEntity()){
                $filter = new \App\Helpers\Filter();
                $inn = $filter->filterInnLegalEntity($inn);
                $typeProfile = IncorporationForm::legalEntity()->getName();
            }

            if ($certificate->isIndividualEntrepreneur()){
                $typeProfile = IncorporationForm::individualEntrepreneur()->getName();
            }

            if ($certificate->isIndividual()){
                $typeProfile = IncorporationForm::individual()->getName();
            }



            return new JsonResponse([
                'inn' => $inn,
                'type_profile' => $typeProfile,
                'snils' => $snils,
                'position' => $certificate->subject_name_position ? $certificate->subject_name_position: '']);
        }

    }

}