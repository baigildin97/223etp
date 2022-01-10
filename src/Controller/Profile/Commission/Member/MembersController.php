<?php
declare(strict_types=1);
namespace App\Controller\Profile\Commission\Member;


use App\Model\User\Entity\Commission\Members\Id;
use App\Model\User\Entity\Commission\Members\MemberRepository;
use App\Model\User\UseCase\Commission\Members;
use App\ReadModel\Profile\Commission\CommissionFetcher;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class MembersController
 * @package App\Controller\Profile\Commission\Member
 */
class MembersController extends AbstractController
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * MembersController constructor.
     * @param TranslatorInterface $translator
     * @param LoggerInterface $logger
     */
    public function __construct(TranslatorInterface $translator, LoggerInterface $logger) {
        $this->translator = $translator;
        $this->logger = $logger;
    }

    /**
     * @param Request $request
     * @param string $commission_id
     * @param Members\Create\Handler $handler
     * @param CommissionFetcher $commissionFetcher
     * @return Response
     * @Route("/commission/{commission_id}/member/create", name="member.create")
     */
    public function create(Request $request, string $commission_id, Members\Create\Handler $handler, CommissionFetcher $commissionFetcher): Response {
        $command = new Members\Create\Command($commission_id);
        $form = $this->createForm(Members\Create\Form::class, $command);
        $profileId = $this->getUser()->getProfileId();

        $commission = $commissionFetcher->findDetail($commission_id);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            try {
                $handler->handle($command);
                $this->addFlash('success',  $this->translator->trans('Member add successfully.',[],'exceptions'));
                return $this->redirectToRoute('profile.commission.show', ['profile_id' => $profileId, 'commission_id' => $commission_id]);
            }catch (\DomainException $e){
                $this->logger->error($e->getMessage(), ['exception'=>$e]);
                $this->addFlash('error', $this->translator->trans($e->getMessage(),[],'exceptions'));
            }
        }

        return $this->render('app/profile/commission/member/create.html.twig', [
            'commission' => $commission,
            'form' => $form->createView(),
            'profile_id' => $profileId
        ]);
    }


    /**
     * @param string $commission_id
     * @param string $member_id
     * @param Members\Archived\Handler $handler
     * @return Response
     * @Route("/commission/{commission_id}/member/{member_id}/archived", name="member.archived", methods={"POST"})
     */
    public function archived(string $commission_id, string $member_id, Members\Archived\Handler $handler): Response {
        $command = new Members\Archived\Command($member_id);
        try {
            $handler->handle($command);
            $this->addFlash('success',  $this->translator->trans('Member archived successfully.',[],'exceptions'));
            return $this->redirectToRoute('commission.show', ['commission_id' => $commission_id]);
        }catch (\DomainException $e){
            $this->logger->error($e->getMessage(), ['exception'=>$e]);
            $this->addFlash('error', $this->translator->trans($e->getMessage(),[],'exceptions'));
        }
    }

    /**
     * @param Request $request
     * @param string $member_id
     * @param string $commission_id
     * @param CommissionFetcher $commissionFetcher
     * @param MemberRepository $memberRepository
     * @param Members\Edit\Handler $handler
     * @return Response
     * @Route("/commission/{commission_id}/member/{member_id}/edit", name="member.edit")
     */
    public function edit(Request $request, string $member_id, string $commission_id, CommissionFetcher $commissionFetcher, MemberRepository $memberRepository, Members\Edit\Handler $handler): Response {
        $member = $memberRepository->get(new Id($member_id));
        $profileId = $this->getUser()->getProfileId();
        $command = Members\Edit\Command::form($member);
        $commission = $commissionFetcher->findDetail($commission_id);
        $form = $this->createForm(Members\Edit\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            try {
                $handler->handle($command);
                $this->addFlash('success',  $this->translator->trans('Member changed successfully.',[],'exceptions'));
                return $this->redirectToRoute('profile.commission.show', ['profile_id'=>$profileId, 'commission_id' => $commission_id]);
            }catch (\DomainException $e){
                $this->logger->error($e->getMessage(), ['exception'=>$e]);
                $this->addFlash('error', $this->translator->trans($e->getMessage(),[],'exceptions'));
            }
        }

        return $this->render('app/profile/commission/member/edit.html.twig', [
            'commission' => $commission,
            'member' => $member,
            'form' => $form->createView(),
            'profile_id' => $profileId
        ]);
    }

}