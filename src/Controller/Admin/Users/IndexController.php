<?php
declare(strict_types=1);

namespace App\Controller\Admin\Users;

use App\Model\Admin\UseCase\Users\Lock\Command;
use App\Model\Admin\UseCase\Users\Lock\Handler;
use App\ReadModel\Certificate\CertificateFetcher;
use App\ReadModel\User\Filter\Filter;
use App\ReadModel\User\Filter\Form;
use App\ReadModel\User\UserFetcher;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class IndexController
 * @package App\Controller\Admin\Users
 * @IsGranted("ROLE_MODERATOR")
 */
class IndexController extends AbstractController
{
    private const PER_PAGE = 15;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * IndexController constructor.
     * @param LoggerInterface $logger
     * @param TranslatorInterface $translator
     */
    public function __construct(LoggerInterface $logger, TranslatorInterface $translator)
    {
        $this->logger = $logger;
        $this->translator = $translator;
    }

    /**
     * Вывод всех юзеров ЭЦП
     * @param Request $request
     * @param UserFetcher $userFetcher
     * @return Response
     * @Route("/admin/users", name="admin.users")
     */
    public function index(Request $request, UserFetcher $userFetcher): Response
    {
        $filter = new Filter();
        $form = $this->createForm(Form::class, $filter);
        $form->handleRequest($request);

        $pagination = $userFetcher->all(
            $filter,
            $request->query->getInt('page', 1),
            self::PER_PAGE,
            $request->get('sort'),
            $request->get('direction')
        );

        return $this->render('app/admin/users/index.html.twig', [
            'form' => $form->createView(),
            'pagination' => $pagination
        ]);
    }


    /**
     * Детальный просмотр юзера
     * @param Request $request
     * @param string $user_id
     * @param UserFetcher $userFetcher
     * @param CertificateFetcher $certificateFetcher
     * @return Response
     * @Route("/admin/users/{user_id}/show", name="admin.users.show")
     */
    public function show(Request $request, string $user_id, UserFetcher $userFetcher, CertificateFetcher $certificateFetcher): Response
    {
        $user = $userFetcher->findDetail($user_id);

        $certificates = $certificateFetcher->activeCertificateListForUser($user_id);
        return $this->render('app/admin/users/show.html.twig', [
            'user' => $user,
            'certificates' => $certificates
        ]);
    }

    /**
     * Назначения роля юзеру
     * @param Request $request
     * @param string $user_id
     * @param UserFetcher $userFetcher
     * @param \App\Model\Admin\UseCase\Users\Edit\Handler $handler
     * @return Response
     * @Route("/admin/users/{user_id}/edit", name="admin.users.edit")
     */
    public function editRole(Request $request, string $user_id, UserFetcher $userFetcher, \App\Model\Admin\UseCase\Users\Edit\Handler $handler): Response
    {
        $this->isGranted('ROLE_ADMIN');
        $user = $userFetcher->findDetail($user_id);

        $form = $this->createForm(\App\Model\Admin\UseCase\Users\Edit\Form::class, $command = new \App\Model\Admin\UseCase\Users\Edit\Command($user_id));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Роль пользователю успешно начначен');
                return $this->redirectToRoute('admin.users.show', ['user_id' => $user_id]);
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
            }
        }

        return $this->render('app/admin/users/update.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }


    /**
     * Подписание контракта с пользователем
     * @param Request $request
     * @param string $user_id
     * @param UserFetcher $userFetcher
     * @param \App\Model\Admin\UseCase\Users\Contract\Handler $handler
     * @return Response
     * @Route("/admin/users/{user_id}/contract", name="admin.users.contract")
     */
    public function contract(Request $request, string $user_id, UserFetcher $userFetcher, \App\Model\Admin\UseCase\Users\Contract\Handler $handler): Response
    {
        $user = $userFetcher->findDetail($user_id);

        $form = $this->createForm(\App\Model\Admin\UseCase\Users\Contract\Form::class,
            $command = new \App\Model\Admin\UseCase\Users\Contract\Command($user_id));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Договор с пользователем подписан');
                return $this->redirectToRoute('admin.users.show', ['user_id' => $user_id]);
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
            }
        }

        return $this->render('app/admin/users/contract.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }


    /**
     * @param Request $request
     * @param string $user_id
     * @param UserFetcher $userFetcher
     * @param \App\Model\Admin\UseCase\Users\Send\Handler $handler
     * @return Response
     * @Route("/admin/users/{user_id}/send", name="admin.users.send")
     */
    public function sendMessages(Request $request, string $user_id, UserFetcher $userFetcher, \App\Model\Admin\UseCase\Users\Send\Handler $handler): Response
    {
        $this->isGranted('ROLE_ADMIN');
        $user = $userFetcher->findDetail($user_id);
        $form = $this->createForm(\App\Model\Admin\UseCase\Users\Send\Form::class, $command = new \App\Model\Admin\UseCase\Users\Send\Command($user_id));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Сообщение успешно отправлено');
                return $this->redirectToRoute('admin.users.show', ['user_id' => $user_id]);
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
            }
        }

        return $this->render('app/admin/users/send.html.twig', [
                'user' => $user,
                'form' => $form->createView()
            ]
        );


    }


    /**
     * Блокировка юзера
     * @param Request $request
     * @param string $user_id
     * @param Handler $handler
     * @param UserFetcher $userFetcher
     * @return Response
     * @Route("/admin/users/{user_id}/{type}", name="admin.users.lock", methods={"POST", "GET"})
     */
    public function lock(Request $request, string $user_id, Handler $handler, UserFetcher $userFetcher): Response
    {
        $this->isGranted('ROLE_ADMIN');
        $type = $request->get('type');

        $form = $this->createForm(\App\Model\Admin\UseCase\Users\Lock\Form::class, $command = new Command($user_id));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                if ($type == 'locked') {
                    $handler->handleLocked($command);
                    $this->addFlash('success', 'Пользователь успешно заблокирован');
                } elseif ($type == 'unlocked') {
                    $handler->handleUnlocked($command);
                    $this->addFlash('success', 'Пользователь успешно разаблокирован');
                }
                return $this->redirectToRoute('admin.users');
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
            }
        }

        $user = $userFetcher->findDetail($user_id);

        return $this->render('app/admin/users/lock.html.twig', [
                'user' => $user,
                'form' => $form->createView()
            ]
        );
    }
}