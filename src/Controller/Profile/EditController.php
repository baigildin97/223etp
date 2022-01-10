<?php
declare(strict_types=1);

namespace App\Controller\Profile;


use App\Model\User\Entity\Profile\Requisite\Requisite;
use App\Model\User\Entity\Profile\Requisite\Status as RequisiteStatus;
use App\Model\User\Entity\Profile\Status;
use App\Model\User\UseCase\Profile\Edit\EditBank\Command;
use App\Model\User\UseCase\Profile\Edit\EditBank\Form;
use App\Model\User\UseCase\Profile\Edit\EditBank\Handler;
use App\Model\User\UseCase\Profile\Edit\Individual\Command as CommandChangeIndividual;
use App\Model\User\UseCase\Profile\Edit\Individual\Form as FormChangeIndividual;
use App\Model\User\UseCase\Profile\Edit\Individual\Handler as HandlerChangeIndividual;
use App\Model\User\UseCase\Profile\Edit\IndividualEntrepreneur\Command as CommandChangeIndividualEntrepreneur;
use App\Model\User\UseCase\Profile\Edit\IndividualEntrepreneur\Form as FormChangeIndividualEntrepreneur;
use App\Model\User\UseCase\Profile\Edit\IndividualEntrepreneur\Handler as HandlerChangeIndividualEntrepreneur;
use App\ReadModel\Certificate\CertificateFetcher;
use App\ReadModel\Profile\ProfileFetcher;
use App\Security\Voter\ProfileAccess;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Model\User\UseCase\Profile\Edit\AddBank\Handler as AddHandler;
use App\Model\User\UseCase\Profile\Edit\AddBank\Form as AddForm;
use App\Model\User\UseCase\Profile\Edit\AddBank\Command as AddCommand;
use App\Model\User\UseCase\Profile\Edit\EditRepresentative\Handler as ReprHandler;
use App\Model\User\UseCase\Profile\Edit\EditRepresentative\Command as ReprCommand;
use App\Model\User\UseCase\Profile\Edit\EditRepresentative\Form as ReprForm;
use Zend\EventManager\Exception\DomainException;

class EditController extends AbstractController
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var ProfileFetcher
     */
    private $profileFetcher;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * EditController constructor.
     * @param TranslatorInterface $translator
     * @param ProfileFetcher $profileFetcher
     * @param LoggerInterface $logger
     */
    public function __construct(TranslatorInterface $translator, ProfileFetcher $profileFetcher, LoggerInterface $logger)
    {
        $this->translator = $translator;
        $this->profileFetcher = $profileFetcher;
        $this->logger = $logger;
    }


    /**
     * Изменения профиля юридического лица
     * @param Request $request
     * @param string $profile_id
     * @param \App\Model\User\UseCase\Profile\Edit\LegalEntity\Handler $handler
     * @return Response
     * @Route("/profile/{profile_id}/edit/legal-entity", name="profile.edit.legal.entity")
     */
    public function editLegalEntity(Request $request, string $profile_id, \App\Model\User\UseCase\Profile\Edit\LegalEntity\Handler $handler): Response
    {

        $this->denyAccessUnlessGranted(
            ProfileAccess::PROFILE_INDEX,
            $profile = $this->profileFetcher->find($profile_id)
        );

        $userId = $this->getUser()->getId();
        $command = \App\Model\User\UseCase\Profile\Edit\LegalEntity\Command::edit(
            $profile,
            $request->getClientIp()
        );

        $form = $this->createForm(\App\Model\User\UseCase\Profile\Edit\LegalEntity\Form::class, $command, ['user_id' => $userId]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                if ($profile->getStatus()->isActive()) {
                    $this->addFlash('success', $this->translator->trans('Изменения данных пользователя Вами внесены. Для активации профиля необходимо отправить заявление на редактирование Оператору ЭТП.', [], 'exceptions'));
                } else {
                    $this->addFlash('success', $this->translator->trans('Изменения данных пользователя Вами внесены. Проверьте прикрепляемые регистрационные документы, подпишите и отправьте заявление на регистрацию Оператору ЭТП', [], 'exceptions'));
                }

                return $this->redirectToRoute('profile', ['profile_id' => $profile_id]);
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
            }
        }

        return $this->render('app/profile/edit/legal_entity.html.twig', [
            'form' => $form->createView(),
            'profile' => $profile
        ]);
    }

    /**
     * Изменения профиля индивидуального предпринимателя
     * @param Request $request
     * @param string $profile_id
     * @param HandlerChangeIndividualEntrepreneur $handler
     * @return Response
     * @Route("/profile/{profile_id}/edit/individual-entrepreneur", name="profile.edit.individual.entrepreneur")
     */
    public function editIndividualEntrepreneur(
        Request $request,
        string $profile_id,
        HandlerChangeIndividualEntrepreneur $handler
    ): Response
    {
        $this->denyAccessUnlessGranted(
            ProfileAccess::PROFILE_INDEX,
            $profile = $this->profileFetcher->find($profile_id)
        );
        $userId = $this->getUser()->getId();

        $command = CommandChangeIndividualEntrepreneur::edit(
            $profile,
            $request->getClientIp()
        );

        $form = $this->createForm(FormChangeIndividualEntrepreneur::class, $command, ['user_id' => $userId]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);

                if ($profile->getStatus()->isActive()) {
                    $this->addFlash('success', $this->translator->trans('Изменения данных пользователя Вами внесены. Для активации профиля необходимо отправить заявление на редактирование Оператору ЭТП.', [], 'exceptions'));
                } else {
                    $this->addFlash('success', $this->translator->trans('Изменения данных пользователя Вами внесены. Проверьте прикрепляемые регистрационные документы, подпишите и отправьте заявление на регистрацию Оператору ЭТП', [], 'exceptions'));
                }
                return $this->redirectToRoute('profile', ['profile_id' => $profile_id]);
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
            }
        }

        return $this->render('app/profile/edit/individual_entrepreneur.html.twig', [
            'form' => $form->createView(),
            'profile' => $this->profileFetcher->find($profile_id)
        ]);
    }

    /**
     * Изменения профиля физическое лицо
     * @param Request $request
     * @param string $profile_id
     * @param HandlerChangeIndividual $handler
     * @return Response
     * @Route("/profile/{profile_id}/edit/individual", name="profile.edit.individual")
     */
    public function editIndividual(
        Request $request,
        string $profile_id,
        HandlerChangeIndividual $handler
    ): Response
    {
        $this->denyAccessUnlessGranted(
            ProfileAccess::PROFILE_INDEX,
            $profile = $this->profileFetcher->find($profile_id)
        );

        $userId = $this->getUser()->getId();

        $command = CommandChangeIndividual::edit(
            $profile,
            $request->getClientIp()
        );

        $form = $this->createForm(FormChangeIndividual::class, $command, ['user_id' => $userId]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                if ($profile->getStatus()->isActive()) {
                    $this->addFlash('success', $this->translator->trans('Изменения данных пользователя Вами внесены. Для активации профиля необходимо отправить заявление на редактирование Оператору ЭТП.', [], 'exceptions'));
                } else {
                    $this->addFlash('success', $this->translator->trans('Изменения данных пользователя Вами внесены. Проверьте прикрепляемые регистрационные документы, подпишите и отправьте заявление на регистрацию Оператору ЭТП', [], 'exceptions'));
                }
                return $this->redirectToRoute('profile', ['profile_id' => $profile_id]);
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
            }
        }

        return $this->render('app/profile/edit/individual.html.twig', [
            'form' => $form->createView(),
            'profile' => $this->profileFetcher->find($profile_id)
        ]);
    }


    /**
     * @Route("profile/edit-bank-info/{requisite_id}", name="profile.edit.bank")
     * @param Request $request
     * @param Handler $handler
     * @param ProfileFetcher $profileFetcher
     * @param string $requisite_id
     * @return Response
     */
    public function editBank(Request $request, Handler $handler, ProfileFetcher $profileFetcher, string $requisite_id): Response
    {


        $user_id = $this->getUser()->getId();
        $requisite = $profileFetcher->getRequisites($this->getUser()->getProfileId(), $requisite_id);

        $command = Command::toEditBankInfo($user_id, $requisite['bank_name'], $requisite['bank_bik'],
            $requisite['payment_account'], $requisite['correspondent_account']);

        $form = $this->createForm(Form::class, $command, ['user_id' => $user_id]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $command->requisite_id = $requisite_id;
                $handler->handle($command);
                $this->addFlash('success', $this->translator->trans('Requisite updated successfully.', [], 'exceptions'));

                return $this->redirectToRoute('profile');
            } catch (\DomainException $e) {
                $this->addFlash('error', 'Не удалось изменить реквизиты');

                return $this->redirectToRoute('profile');
            }
        }

        return $this->render('app/profile/edit/editBank.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @param Request $request
     * @param AddHandler $handler
     * @return Response
     * @Route("profile/add-bank-info", name="profile.add.bank")
     */
    public function addBank(Request $request, AddHandler $handler, ProfileFetcher $profileFetcher): Response
    {
        $this->denyAccessUnlessGranted(
            ProfileAccess::PROFILE_INDEX,
            $profile = $this->profileFetcher->findDetailByUserId($this->getUser()->getId())
        );

        $form = $this->createForm(AddForm::class, $command = new AddCommand($profile->id));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', $this->translator->trans('Requisite add successfully.', [], 'exceptions'));
                return $this->redirectToRoute('profile');
            } catch (\DomainException $e) {
                $this->addFlash('error', $e->getMessage());
                return $this->redirectToRoute('profile');
            }
        }

        return $this->render('app/profile/edit/editBank.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @param string $requisite_id
     * @return Response
     * @Route("/profile/delete-bank/{requisite_id}", name="profile.delete.bank")
     */
    public function deleteBank(string $requisite_id): Response
    {
        $requisiteManager = $this->getDoctrine()->getManagerForClass(Requisite::class);
        $requisite = $requisiteManager->find(Requisite::class, $requisite_id);

        $requisite->setStatus(RequisiteStatus::inactive());
        $requisite->setChangedAt(new \DateTimeImmutable());

        $requisiteManager->persist($requisite);
        $requisiteManager->flush();

        return $this->redirectToRoute('profile');
    }

    /**
     * @param Request $request
     * @param ReprHandler $handler
     * @param ProfileFetcher $profileFetcher
     * @return Response
     * @Route("/profile/edit-representative", name="profile.edit.representative")
     */
    public function editRepresentative(Request $request, ReprHandler $handler, ProfileFetcher $profileFetcher): Response
    {
        $profile_id = $this->getUser()->getProfileId();
        $profile = $profileFetcher->findDetailByUserId($this->getUser()->getId());

        if (in_array($profile->status,
            [Status::moderate()->getName(), Status::blocked()->getName(), Status::archived()->getName()])) {

            $this->addFlash('error', 'Нельзя редактировать профиль находящийся на модерации
             или который был заблокирован или удален');

            return $this->redirectToRoute('profile');
        }

        $command = ReprCommand::toEditReprInfo($profile->position, $profile->confirming_document, $profile->phone,
            $profile->repr_email, $profile->passport_series, $profile->passport_number, $profile->passport_issuer,
            \DateTimeImmutable::createFromFormat('Y-m-d', $profile->passport_issuance_date ?? '2010-01-01'),
            $profile->passport_unit_code, $profile->passport_citizenship,
            \DateTimeImmutable::createFromFormat('Y-m-d', $profile->passport_birth_day ?? '2010-01-01'), $profile->repr_name);

        $form = $this->createForm(ReprForm::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $command->profile_id = $profile_id;
                $handler->handle($command);

                if (in_array($profile->status, [Status::rejected()->getName(), Status::active()->getName()]))
                    return $this->redirectToRoute('profile.accreditation');
                else
                    return $this->redirectToRoute('profile');
            } catch (\DomainException $e) {
                $this->addFlash('error', $e->getMessage());
                return $this->redirectToRoute('profile');
            }
        }

        return $this->render('app/profile/edit/editRepresentative.html.twig', [
            'form' => $form->createView()
        ]);
    }
}