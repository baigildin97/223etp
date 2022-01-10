<?php
declare(strict_types=1);
namespace App\Controller\Admin\Settings;

use App\Model\Admin\UseCase\Settings\Add;
use App\Model\Admin\UseCase\Settings\Edit;
use App\ReadModel\Admin\Settings\Filter\Filter;
use App\ReadModel\Admin\Settings\SettingsFetcher;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Class IndexController
 * @package App\Controller\Admin\Settings
 * @IsGranted("ROLE_MODERATOR")
 */
class IndexController extends AbstractController
{
    private const PER_PAGE = 10;

    private $logger;
    private $translator;

    public function __construct(LoggerInterface $logger, TranslatorInterface $translator)
    {
        $this->logger = $logger;
        $this->translator = $translator;

    }

    /**
     * @param Request $request
     * @param SettingsFetcher $settingsFetcher
     * @return Response
     * @Route("/admin/settings", name="settings")
     */
    public function index(Request $request, SettingsFetcher $settingsFetcher): Response {

        $settings = $settingsFetcher->all(
            new Filter(),
            $request->query->getInt('page',1),
            self::PER_PAGE
        );

        return $this->render('app/admin/settings/index.html.twig', [
            'settings' => $settings
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/admin/settings/add", name="settings.add")
     */
    public function add(Request $request, Add\Handler $handler): Response{
        $this->isGranted('ROLE_ADMIN');
        $form = $this->createForm(Add\Form::class, $command = new Add\Command());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            try{
                $handler->handle($command);
                $this->addFlash('success', $this->translator->trans('Setting added successfully.',[],'exceptions'));
                return $this->redirectToRoute('settings');
            }catch (\DomainException $exception){
                $this->logger->error($exception->getMessage(), ['exception' => $exception]);
                $this->addFlash('error',$this->translator->trans($exception->getMessage(),[],'exceptions'));
            }
        }
        return $this->render('app/admin/settings/add.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @param string $settings_id
     * @param Edit\Handler $handler
     * @return Response
     * @Route("/admin/settings/{settings_id}/edit", name="settings.edit")
     */
    public function edit(Request $request, string $settings_id, Edit\Handler $handler, SettingsFetcher $settingsFetcher): Response {
        $this->isGranted('ROLE_ADMIN');
        $setting = $settingsFetcher->findDetail($settings_id);
        $form = $this->createForm(Edit\Form::class, $command = Edit\Command::edit($settings_id, $setting->value));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            try{
                $handler->handle($command);
                $this->addFlash('success', $this->translator->trans('Setting edit successfully.',[],'exceptions'));
                return $this->redirectToRoute('settings');
            }catch (\DomainException $exception){
                $this->logger->error($exception->getMessage(), ['exception' => $exception]);
                $this->addFlash('error',$this->translator->trans($exception->getMessage(),[],'exceptions'));
            }
        }
        return $this->render('app/admin/settings/edit.html.twig',[
            'form' => $form->createView(),
            'setting' => $setting
        ]);
    }

}