<?php
declare(strict_types=1);
namespace App\Controller\Admin\Roles;


use App\Controller\ErrorHandler;
use App\Model\User\Entity\Profile\Role\Permission;
use App\Model\User\Entity\Profile\Role\Role;
use App\ReadModel\Admin\Roles\RoleFetcher;
use App\Services\Main\GlobalRoleAccessor;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Model\Admin\UseCase\Role\Copy;
use App\Model\Admin\UseCase\Role\Create;
use App\Model\Admin\UseCase\Role\Edit;
use App\Model\Admin\UseCase\Role\Remove;

/**
 * Class IndexController
 * @package App\Controller\Admin\Roles
 * @IsGranted("ROLE_MODERATOR")
 */
class IndexController extends AbstractController
{
    private $errors;
    private $roleFetcher;

    public function __construct(ErrorHandler $errors, RoleFetcher $roleFetcher, GlobalRoleAccessor $globalRoleAccessor)
    {
        $this->errors = $errors;
        $this->roleFetcher = $roleFetcher;
    }

    /**
     * @Route("/admin/roles", name="roles")
     * @param RoleFetcher $fetcher
     * @return Response
     */
    public function index(RoleFetcher $fetcher): Response
    {
        $roles = $fetcher->all();
        $permissions = Permission::names();
        return $this->render('app/admin/roles/index.html.twig', compact('roles', 'permissions'));
    }

    /**
     * @Route("/admin/role/create", name="role.create")
     * @param Request $request
     * @param Create\Handler $handler
     * @return Response
     */
    public function create(Request $request, Create\Handler $handler): Response
    {
        $this->isGranted('ROLE_ADMIN');
        $command = new Create\Command();

        $form = $this->createForm(Create\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('roles');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/admin/roles/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/role/{id}/edit", name="role.edit")
     * @param string $id
     * @param Request $request
     * @param Edit\Handler $handler
     * @return Response
     */
    public function edit(string $id, Request $request, Edit\Handler $handler): Response
    {
        $this->isGranted('ROLE_ADMIN');
        $role = $this->roleFetcher->findDetail($id);
        $command = Edit\Command::fromRole($role->id, $role->name, $role->role_constant, $role->permissions);
        $form = $this->createForm(Edit\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('role.show', ['id' => $role->id]);
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/admin/roles/edit.html.twig', [
            'role' => $role,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/role/{id}/copy", name="role.copy")
     * @param string $id
     * @param Request $request
     * @param Copy\Handler $handler
     * @return Response
     */
    public function copy(string $id, Request $request, Copy\Handler $handler): Response
    {
        $this->isGranted('ROLE_ADMIN');
        $role = $this->roleFetcher->findDetail($id);

        $command = new Copy\Command($role->id);

        $form = $this->createForm(Copy\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('roles');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/admin/roles/copy.html.twig', [
            'role' => $role,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/role/{id}/delete", name="role.delete", methods={"POST"})
     * @param Role $role
     * @param Request $request
     * @param Remove\Handler $handler
     * @return Response
     */
    public function delete(Role $role, Request $request, Remove\Handler $handler): Response
    {
        $this->isGranted('ROLE_ADMIN');
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('role.show', ['id' => $role->getId()]);
        }

        $command = new Remove\Command($role->getId()->getValue());

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('roles');
    }

    /**
     * @Route("/admin/role/{id}", name="role.show")
     * @param string $id
     * @return Response
     */
    public function show(string $id): Response
    {
        return $this->render('app/admin/roles/show.html.twig', [
            'role' => $this->roleFetcher->findDetail($id)
        ]);
    }
}