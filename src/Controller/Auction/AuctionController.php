<?php
declare(strict_types=1);

namespace App\Controller\Auction;

use App\Helpers\Filter;
use App\Model\Admin\Entity\Settings\Key;
use App\Model\User\Entity\Profile\Role\Permission;
use App\Model\User\Entity\User\Notification\Category;
use App\Model\User\Entity\User\Notification\NotificationType;
use App\Model\Work\Procedure\Entity\Lot\Auction\AuctionRepository;
use App\Model\Work\Procedure\Services\Auction\Temp\OfferXmlGenerator;
use App\Model\Work\Procedure\Services\Bid\Confirm\SignXmlGenerator;
use App\Model\Work\Procedure\Services\Bid\Confirm\XmlDetailView;
use App\Model\Work\Procedure\UseCase\Auction\Bet\Command;
use App\Model\Work\Procedure\UseCase\Auction\Bet\Handler;
use App\Model\Work\Procedure\UseCase\Auction\Confirm\Form;
use App\ReadModel\Admin\Settings\SettingsFetcher;
use App\ReadModel\Auction\AuctionFetcher;
use App\ReadModel\Auction\Offers\OffersFetcher;
use App\ReadModel\Procedure\Bid\BidFetcher;
use App\ReadModel\Procedure\Lot\LotFetcher;
use App\ReadModel\Profile\ProfileFetcher;
use App\Services\Hash\Streebog;
use App\Helpers\FormatMoney;
use App\Services\Tasks\Notification;
use Doctrine\DBAL\Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProcedureController
 * @package App\Controller\Auction
 */
class AuctionController extends AbstractController
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
     * @var ProfileFetcher
     */
    private $profileFetcher;

    /**
     * @var AuctionFetcher
     */
    private $auctionFetcher;

    /**
     * ProcedureController constructor.
     * @param TranslatorInterface $translator
     * @param LoggerInterface $logger
     * @param ProfileFetcher $profileFetcher
     * @param AuctionFetcher $auctionFetcher
     */
    public function __construct(
        TranslatorInterface $translator,
        LoggerInterface $logger,
        ProfileFetcher $profileFetcher,
        AuctionFetcher $auctionFetcher
    )
    {
        $this->translator = $translator;
        $this->logger = $logger;
        $this->profileFetcher = $profileFetcher;
        $this->auctionFetcher = $auctionFetcher;
    }

    /**
     * Аукционный зал
     * @param Request $request
     * @param string $lotId
     * @param string $auctionId
     * @param AuctionFetcher $auctionFetcher
     * @param LotFetcher $lotFetcher
     * @param BidFetcher $bidFetcher
     * @param OffersFetcher $offersFetcher
     * @param AuctionRepository $auctionRepository
     * @param SettingsFetcher $settingsFetcher
     * @param Notification $notification
     * @return Response
     * @Route("/lot/{lotId}/auction/{auctionId}", name="auction.show")
     */
    public function show(
        Request $request,
        string $lotId,
        string $auctionId,
        AuctionFetcher $auctionFetcher,
        LotFetcher $lotFetcher,
        BidFetcher $bidFetcher,
        OffersFetcher $offersFetcher,
        AuctionRepository $auctionRepository,
        SettingsFetcher $settingsFetcher,
    Notification $notification
    ): Response
    {

    /*    foreach ($auction->getLot()->getApprovedBids() as $bid) {
            $notification->createOne(
                NotificationType::completedAuction($auction->getLot()->getFullNumber()),
                Category::categoryOne(),
                $bid->getParticipant()->getUser()
            );
        }*/
        $profile = $this->profileFetcher->findDetailByUserId($userId = $this->getUser()->getId());
        $auction = $auctionFetcher->findDetail($auctionId);
        $this->denyAccessUnlessGranted(Permission::SHOW_AUCTION, [
            'profile' => $profile,
            'lot_id' => $lotId,
            'auction' => $auction
        ]);

        if($profile !== null) {
            $findMyBind = $bidFetcher->findMyBid($lotId, $profile->id);
            if ($findMyBind !== null) {
                $findMyOffer = $offersFetcher->findMyOffer($findMyBind->id);
                $currentParticipantBid = $bidFetcher->findDetail($findMyBind->id);

                if ($settingsFetcher->findDetailByKey(Key::confirmationParticipationAuction()) === Key::confirmationParticipationAuction()->getValue()) {
                    if (!$currentParticipantBid->isConfirmedXmlParticipant()) {
                        $this->addFlash('error', 'Подтвердите пожалуйста свое участие!');
                        return $this->redirectToRoute('auction.confirm.participation', [
                            'bid_id' => $findMyBind->id,
                            'auctionId' => $auctionId
                        ]);
                    }
                }

            }
        }



        if ($auction->isActive()) {
            $this->addFlash('error', 'Доступ запрещен.');
            return $this->redirectToRoute('lot.show', ['lotId' => $lotId]);
        }

        $lot = $lotFetcher->findDetail($auction->lot_id);

        $findAuctionOffers = $offersFetcher->all(
            \App\ReadModel\Auction\Offers\Filter\Filter::forAuctionId($auctionId),
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );

        $findLastOffer = $offersFetcher->findLastOffer($auctionId);

        return $this->render('app/auctions/show.html.twig', [
            'auction' => $auction,
            'lot' => $lot,
            'bid' => $currentParticipantBid ?? '',
            'offers' => $findAuctionOffers,
            'my_offer' => $findMyOffer ?? null,
            'last_offer' => $findLastOffer
        ]);
    }

    /**
     * Динамическое обновления зала аукциона
     * @param Request $request
     * @param string $lot_id
     * @param string $auction_id
     * @param BidFetcher $bidFetcher
     * @param OffersFetcher $offersFetcher
     * @param FormatMoney $formatMoney
     * @return JsonResponse
     * @throws \Exception
     * @Route("/lot/{lot_id}/auction/{auction_id}/offers", name="auction.offers.ajax")
     */
    public function ajaxOffers(
        Request $request,
        string $lot_id,
        string $auction_id,
        BidFetcher $bidFetcher,
        OffersFetcher $offersFetcher,
        FormatMoney $formatMoney
    ): JsonResponse {
        if ($request->isXmlHttpRequest()) {
            $profile = $this->profileFetcher->findDetailByUserId($userId = $this->getUser()->getId());
            $auction = $this->auctionFetcher->findDetail($auction_id);


            $this->denyAccessUnlessGranted(Permission::SHOW_AUCTION, [
                'profile' => $profile,
                'lot_id' => $lot_id,
                'auction' => $auction
            ]);


            if($profile !== null) {
                $findMyBind = $bidFetcher->findMyBid($lot_id, $profile->id ?? '0');
                if ($findMyBind !== null) {
                    $bid = $bidFetcher->findDetail($findMyBind->id);
                    $findMyOffer = $offersFetcher->findMyOffer($bid->id);
                    $currentPosition = $offersFetcher->findCurrentPosition($auction_id, $bid->id);
                }
            }


            $findAuctionOffers = $offersFetcher->all(
                \App\ReadModel\Auction\Offers\Filter\Filter::forAuctionId($auction_id),
                $request->query->getInt('page', 1),
                self::PER_PAGE
            );

            $offersRender = $this->renderView("app/auctions/offersAjax.html.twig", [
                'offers' => $findAuctionOffers
            ]);

            $findLastOffer = $offersFetcher->findLastOffer($auction_id);
            $nextCost = $formatMoney->money($auction->current_cost)->add($formatMoney->money($auction->auction_step));
            $statePercent = round(($formatMoney->decimal($auction->current_cost) * 100) / $formatMoney->decimal($auction->starting_price), 2);

            $response = [
                'auction' => $auction,
                'current_position' => $currentPosition ?? "0",
                'offersHtml' => $offersRender,
                'state_percent' => $statePercent ?? "0",
                'closing_time' => strtotime($auction->default_closed_time),
                'default_closed_time' => Filter::date($auction->default_closed_time),
                'next_cost' => $formatMoney->currencyAsSymbol($nextCost->getCurrency() . " " . $nextCost->getAmount()),
                'last_offer_cost' => isset($findLastOffer->cost) ? $formatMoney->currencyAsSymbol($findLastOffer->cost) : $formatMoney->currencyAsSymbol("RUB 0"),
                'my_offers_cost' => isset($findMyOffer->cost) ? $formatMoney->currencyAsSymbol($findMyOffer->cost) : $formatMoney->currencyAsSymbol("RUB 0")
            ];

            return new JsonResponse($response);
        }
    }


    /**
     * Ставка участника
     * @param Request $request
     * @param string $auction_id
     * @param string $bid_id
     * @param OfferXmlGenerator $offerXmlGenerator
     * @param Handler $handler
     * @return JsonResponse
     * @throws Exception
     * @Route("/auction/{auction_id}/bet/{bid_id}", name="auction.offers.bet.ajax")
     */
    public function ajaxBetParticipant(
        Request $request,
        string $auction_id,
        string $bid_id,
        OfferXmlGenerator $offerXmlGenerator,
        Handler $handler
    ): JsonResponse
    {
        $this->isGranted(Permission::BET_OFFER_AUCTION);
        if ($request->isXmlHttpRequest()) {
            $content = $request->getContent();
            $paramsPost = json_decode($content, true);
            $profile = $this->profileFetcher->findDetailByUserId($this->getUser()->getId());

            $xml = $offerXmlGenerator->generate($bid_id, $auction_id, $paramsPost['cost'], $request->getClientIp());

            $command = new Command(
                $profile->id,
                $auction_id,
                $bid_id,
                $paramsPost['cost'],
                $xml,
                $paramsPost['hash'],
                $paramsPost['sign'],
                $request->getClientIp()
            );

            try {
                $handler->handle($command);
                return new JsonResponse(['success' => 'Ваше ценовое предложение принято.']);
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                return new JsonResponse(['error' => $this->translator->trans($e->getMessage(), [], 'exceptions')]);
            }
        }
        return new JsonResponse(['error' => 'Error']);
    }

    /**
     * Возвращает хэш
     * @param Request $request
     * @param string $auction_id
     * @param string $bid_id
     * @param Streebog $streebog
     * @param OfferXmlGenerator $offerXmlGenerator
     * @return JsonResponse
     * @Route("/auction/{auction_id}/hash/{bid_id}", name="auction.offers.gethash.ajax")
     */
    public function ajaxGetHash(
        Request $request,
        string $auction_id,
        string $bid_id,
        Streebog $streebog,
        OfferXmlGenerator $offerXmlGenerator
    ): JsonResponse
    {
        $this->isGranted(Permission::BET_OFFER_AUCTION);
        if ($request->isXmlHttpRequest()) {
            $requestContent = json_decode($request->getContent(), true);
            $xml = $offerXmlGenerator->generate($bid_id, $auction_id, $requestContent['cost'], $request->getClientIp());
            return new JsonResponse(['hash' => $streebog->getHash($xml)]);
        }
        return new JsonResponse(['error' => 'Error']);
    }

    /**
     * Подтверждения участие с аукционе
     * @param Request $request
     * @param string $bid_id
     * @param string $auctionId
     * @param AuctionFetcher $auctionFetcher
     * @param Streebog $streebog
     * @param SignXmlGenerator $signXmlGenerator
     * @param BidFetcher $bidFetcher
     * @param \App\Model\Work\Procedure\UseCase\Auction\Confirm\Handler $handler
     * @param SerializerInterface $serializer
     * @return RedirectResponse|Response
     * @Route("/bid/{bid_id}/auction/{auctionId}/confirm", name="auction.confirm.participation")
     */
    public function confirmParticipation(
        Request $request,
        string $bid_id,
        string $auctionId,
        AuctionFetcher $auctionFetcher,
        Streebog $streebog,
        SignXmlGenerator $signXmlGenerator,
        BidFetcher $bidFetcher,
        \App\Model\Work\Procedure\UseCase\Auction\Confirm\Handler $handler,
        SerializerInterface $serializer
    )
    {
        $auction = $auctionFetcher->findDetail($auctionId);

        $bid = $bidFetcher->findDetail($bid_id);

        $xml = $signXmlGenerator->generate($bid);
        $hash = $streebog->getHash($xml);

        $form = $this->createForm(
            Form::class,
            $command = new \App\Model\Work\Procedure\UseCase\Auction\Confirm\Command($bid->id, $xml, $hash, $auction->id)
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Вы успешно подтвердили свое участие в аукционе');
                return $this->redirectToRoute('auction.show', [
                    'lotId' => $bid->lot_id,
                    'auctionId' => $auctionId
                ]);
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
            }
        }

        $deserializeXml = $serializer->deserialize($xml, XmlDetailView::class, 'xml');

        return $this->render('app/auctions/confirm.html.twig', [
            'bid' => $bid,
            'xml' => $deserializeXml,
            'hash' => $hash,
            'form' => $form->createView()
        ]);

    }

}
