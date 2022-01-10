<?php


namespace App\Controller\Procedure;


use App\Model\Work\Procedure\Entity\Type;
use App\Model\Work\Procedure\UseCase\Create\Auction\Handler;
use App\Model\Work\Procedure\UseCase\Create\Form;
use App\ReadModel\Procedure\ProcedureFetcher;
use App\ReadModel\Profile\ProfileFetcher;
use App\Services\Main\GlobalRoleAccessor;
use Ekapusta\OAuth2Esia\Security\JWTSigner\TmpFile;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Routing\Annotation\Route;

class CreateController extends AbstractController
{
    private const PER_PAGE = 10;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var tmpfile()
     */
    private $tempfile;

    /**
     * @var GlobalRoleAccessor
     */
    private $globalRoleAccessor;

    public function __construct(TranslatorInterface $translator, LoggerInterface $logger, GlobalRoleAccessor $globalRoleAccessor)
    {
        $this->translator = $translator;
        $this->logger = $logger;
        $this->tempfile = tmpfile();
        $this->globalRoleAccessor = $globalRoleAccessor;
    }

    /**
     * @param Request $request
     * @param ProfileFetcher $profileFetcher
     * @return Response
     * @throws \Doctrine\DBAL\Exception
     * @Route("/procedure/create", name="procedure.create")
     */
    public function start(Request $request, ProfileFetcher $profileFetcher): Response
    {
        if ($profileId = $this->getUser()->getProfileId())
            $profile = $profileFetcher->find($profileId);
        else
        {
            $this->addFlash('error', 'Для создания процедуры необходимо заполнить профиль');
            return $this->redirectToRoute('profile.create');
        }

        if (!$profile->isOrganizer())
        {
            $this->addFlash('error', 'Возможность создавать процедуры доступна только для организаторов торгов');
            return $this->redirectToRoute('home');
        }

        $form = $this->createForm(Form::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $type = $form->getData()['procedureType'];
            if ($type === Type::AUCTION)
                return $this->redirectToRoute('procedure.create.auction');
            if ($type === Type::CONTEST)
                return $this->redirectToRoute('procedure.create.contest');
            if ($type === Type::REQUEST_OFFERS)
                return $this->redirectToRoute('procedure.create.requestOffers');
        }

        return $this->render('app/procedures/create/start.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @param Request $request
     * @param ProfileFetcher $profileFetcher
     * @param Handler $handler
     * @return Response
     * @throws \Doctrine\DBAL\Exception
     * @Route("/procedure/create/auction", name="procedure.create.auction")
     */
    public function auction(Request $request, ProfileFetcher $profileFetcher, Handler $handler, ProcedureFetcher $procedureFetcher): Response
    {
        $organizerProfile = $profileFetcher->find($profileId = $this->getUser()->getProfileId());

        $command = new \App\Model\Work\Procedure\UseCase\Create\Auction\Command(
            $this->getUser()->getId(),
            $request->getClientIp(),
            $newProcedureId = \App\Model\Work\Procedure\Entity\Id::next()->getValue()
        );

        $form = $this->createForm(\App\Model\Work\Procedure\UseCase\Create\Auction\Form::class, $command);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', $this->translator->trans('Procedure added successfully.', [], 'exceptions'));
                return $this->redirectToRoute('procedure.lot.add', ['procedureId' => $newProcedureId]);
            } catch (\DomainException $exception) {
                $this->logger->error($exception->getMessage(), ['exception' => $exception]);
                $this->addFlash('error', $this->translator->trans($exception->getMessage(), [], 'exceptions'));
            }
        }

        return $this->render('app/procedures/create/auction/create.html.twig', [
            'form' => $form->createView(),
            'organizerProfile' => $organizerProfile
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/procedure/create/contest", name="procedure.create.contest")
     */
    public function contest(Request $request): Response
    {

    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/procedure/create/request_offers", name="procedure.create.requestOffers")
     */
    public function requestOffers(Request $request): Response
    {

    }
}