<?php
declare(strict_types=1);
namespace App\Controller\Profile\Commission;


use App\Model\User\Entity\Commission\Commission\CommissionRepository;
use App\Model\User\Entity\Certificate\Id;
use App\Model\User\UseCase\Commission\Commission;
use App\ReadModel\Profile\Commission\CommissionFetcher;
use App\ReadModel\Profile\Commission\Filter;
use App\ReadModel\Profile\Commission\Member\Filter\Form;
use App\ReadModel\Profile\Commission\Member\MemberFetcher;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class CommissionController extends AbstractController
{
    private const PER_PAGE = 10;

    private $translator;

    private $logger;

    public function __construct(TranslatorInterface $translator, LoggerInterface $logger) {
        $this->translator = $translator;
        $this->logger = $logger;
    }

    /**
     * @param Request $request
     * @param string $profile_id
     * @param CommissionFetcher $commissionFetcher
     * @return Response
     * @Route("profile/{profile_id}/commissions", name="profile.commissions")
     */
    public function index(Request $request, string $profile_id, CommissionFetcher $commissionFetcher): Response {
        $filter = new Filter\Filter();
        $form = $this->createForm(Filter\Form::class, $filter);
        $form->handleRequest($request);

        $pagination = $commissionFetcher->all(
            $filter,
            $request->query->getInt('page',1),
            self::PER_PAGE
        );

        return $this->render('app/profile/commission/index.html.twig', [
            'form' => $form->createView(),
            'pagination' => $pagination,
            'profile_id' => $profile_id
        ]);
    }

    /**
     * @param Request $request
     * @param string $profile_id
     * @param Commission\Create\Handler $handler
     * @return Response
     * @throws \Exception
     * @Route("profile/{profile_id}/commission/create", name="profile.commission.create")
     */
    public function create(Request $request, string $profile_id, Commission\Create\Handler $handler): Response {
        $command = new Commission\Create\Command($profile_id);
        $form = $this->createForm(Commission\Create\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            try {
                $handler->handle($command);
                $this->addFlash('success',  $this->translator->trans('Commission added successfully.',[],'exceptions'));
                return $this->redirectToRoute('profile.commissions', ['profile_id' => $profile_id]);
            }catch (\DomainException $e){
                $this->logger->error($e->getMessage(), ['exception'=>$e]);
                $this->addFlash('error', $this->translator->trans($e->getMessage(),[],'exceptions'));
            }
        }

        return $this->render('app/profile/commission/create.html.twig', [
            'form' => $form->createView(),
            'profile_id' => $profile_id
        ]);
    }


    /**
     * @param string $profile_id
     * @param string $commission_id
     * @param Commission\Archived\Handler $handler
     * @return Response
     * @Route("profile/{profile_id}/commission/{commission_id}/archived", name="profile.commission.archived", methods={"POST"})
     */
    public function archived(string $profile_id, string $commission_id, Commission\Archived\Handler $handler): Response {
        $command = new Commission\Archived\Command($commission_id);
        try {
            $handler->handle($command);
            $this->addFlash('success',  $this->translator->trans('Commission archived successfully.',[],'exceptions'));
            return $this->redirectToRoute('profile.commissions', ['profile_id' => $profile_id]);
        }catch (\DomainException $e){
            $this->logger->error($e->getMessage(), ['exception'=>$e]);
            $this->addFlash('error', $this->translator->trans($e->getMessage(),[],'exceptions'));
        }
    }


    /**
     * @param Request $request
     * @param string $profile_id
     * @param string $commission_id
     * @param CommissionFetcher $commissionFetcher
     * @param MemberFetcher $memberFetcher
     * @return Response
     * @Route("profile/{profile_id}/commission/{commission_id}", name="profile.commission.show")
     */
    public function show(Request $request, string $profile_id, string $commission_id, CommissionFetcher $commissionFetcher, MemberFetcher $memberFetcher): Response {
        $filter = new \App\ReadModel\Profile\Commission\Member\Filter\Filter();
        $form = $this->createForm(Form::class, $filter);
        $form->handleRequest($request);

        if (!$commission = $commissionFetcher->findDetail($commission_id)){
            $this->addFlash('success',  $this->translator->trans('Commission not found.',[],'exceptions'));
            return $this->redirectToRoute('commissions');
        }

        $pagination = $memberFetcher->all(
            $filter,
            $request->query->getInt('page',1),
            self::PER_PAGE,
            $commission_id
        );

        return $this->render('app/profile/commission/show.html.twig', [
            'commission' => $commission,
            'pagination' => $pagination,
            'profile_id' => $profile_id,
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @param string $profile_id
     * @param string $commission_id
     * @param CommissionFetcher $commissionFetcher
     * @param Commission\Edit\Handler $handler
     * @return Response
     * @throws \Exception
     * @Route("profile/{profile_id}/commission/{commission_id}/edit", name="profile.commission.edit", methods={"POST", "GET"})
     */
    public function edit(Request $request, string $profile_id, string $commission_id, CommissionFetcher $commissionFetcher, Commission\Edit\Handler $handler): Response {
        $commission = $commissionFetcher->findDetail($commission_id);
        $command = Commission\Edit\Command::edit($profile_id, $commission);

        $form = $this->createForm(Commission\Edit\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            try {
                $handler->handle($command);
                $this->addFlash('success',  $this->translator->trans('Commission changed successfully.',[],'exceptions'));
                return $this->redirectToRoute('profile.commission.show', [
                    'profile_id' => $profile_id,
                    'commission_id' => $commission_id
                ]);
            }catch (\DomainException $e){
                $this->logger->error($e->getMessage(), ['exception'=>$e]);
                $this->addFlash('error', $this->translator->trans($e->getMessage(),[],'exceptions'));
            }
        }

        return $this->render('app/profile/commission/edit.html.twig', [
            'commission' => $commission,
            'profile_id' => $profile_id,
            'form' => $form->createView()
        ]);
    }

}