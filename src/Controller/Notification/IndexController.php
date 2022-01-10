<?php
declare(strict_types=1);
namespace App\Controller\Notification;

use App\Model\User\Entity\User\Notification\Category;
use App\Model\User\Entity\User\Notification\Id;
use App\Model\User\Entity\User\Notification\NotificationType;
use App\Model\User\UseCase\Profile\Notification\Read\Command;
use App\Model\User\UseCase\Profile\Notification\Read\Handler;
use App\ReadModel\Profile\ProfileFetcher;
use App\ReadModel\User\Notification\Filter\Filter;
use App\ReadModel\User\Notification\Filter\Form;
use App\ReadModel\User\Notification\NotificationFetcher;
use App\Security\Voter\NotificationAccess;
use App\Services\Tasks\Notification;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class IndexController
 * @package App\Controller\Notification
 */
class IndexController extends AbstractController
{

    private const PER_PAGE = 50;

    /**
     * @param Request $request
     * @param string $user_id
     * @param NotificationFetcher $notificationFetcher
     * @param ProfileFetcher $profileFetcher
     * @return Response
     * @Route("/notifications/{user_id}", name="notifications")
     */
    public function index(Request $request, string $user_id, NotificationFetcher $notificationFetcher, ProfileFetcher $profileFetcher): Response {

        $this->denyAccessUnlessGranted(
            NotificationAccess::NOTIFICATION_SHOW,
            $profile = $profileFetcher->findDetailByUserId($user_id)
        );

        $filter = Filter::forUserId($user_id);

        $form = $this->createForm(Form::class, $filter);
        $form->handleRequest($request);

        $pagination = $notificationFetcher->all(
            $filter,
            $request->query->getInt('page',1),
            self::PER_PAGE
        );

        return $this->render('app/notification/index.html.twig',[
            'notifications' => $pagination,
            'form' => $form->createView(),
            'user_id' => $user_id
        ]);
    }

    /**
     * Функция пометки прочитанного уведомления
     * @param Request $request
     * @param string $id_notification
     * @param Handler $handler
     * @return JsonResponse
     * @Route("/notifications/read/{id_notification}", name="notification.read")
     */
    public function read(Request $request, string $id_notification, Handler $handler): JsonResponse{

        $handler->handle(new Command(new Id($id_notification)));

        $data['status'] = "success";

        return new JsonResponse($data);
    }

}