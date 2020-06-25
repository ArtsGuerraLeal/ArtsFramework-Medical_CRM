<?php

namespace App\Controller;

use App\Entity\CompanyUsers;
use App\Entity\User;
use App\Form\CompanyUsersType;
use App\Repository\CompanyUsersRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/users")
 */
class CompanyUsersController extends AbstractController
{

    /**
     * @var UserRepository
     */
    private $userRepository;

    private  $security;

    public function __construct(UserRepository $userRepository,Security $security){
      $this->userRepository = $userRepository;
      $this->security = $security;

    }

    /**
     * @Route("/", name="company_users_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_COMPANY_ADMIN');

        $user = $this->security->getUser();
        return $this->render('company_users/index.html.twig', [
            'users' => $userRepository->findByCompany($user->getCompany()),
        ]);
    }

    /**
     * @Route("/new", name="company_users_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_COMPANY_ADMIN');

        $companyUser = new CompanyUsers();
        $form = $this->createForm(CompanyUsersType::class, $companyUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($companyUser);
            $entityManager->flush();

            return $this->redirectToRoute('company_users_index');
        }

        return $this->render('company_users/new.html.twig', [
            'company_user' => $companyUser,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="company_users_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        $this->denyAccessUnlessGranted('ROLE_COMPANY_ADMIN');

        return $this->render('company_users/show.html.twig', [
            'company_user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/authorize", name="company_users_authorize", methods={"GET","POST"})
     * @param User $user
     * @return Response
     */
    public function authorize(User $user): Response
    {
        $this->denyAccessUnlessGranted('ROLE_COMPANY_ADMIN');

        $user->setRoles(["ROLE_COMPANY_USER"]);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('company_users_index');
    }

    /**
     * @Route("/{id}/edit", name="company_users_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, CompanyUsers $companyUser): Response
    {
        $this->denyAccessUnlessGranted('ROLE_COMPANY_ADMIN');

        $form = $this->createForm(CompanyUsersType::class, $companyUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('company_users_index');
        }

        return $this->render('company_users/edit.html.twig', [
            'company_user' => $companyUser,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="company_users_delete", methods={"DELETE"})
     */
    public function delete(Request $request, CompanyUsers $companyUser): Response
    {
        $this->denyAccessUnlessGranted('ROLE_COMPANY_ADMIN');

        if ($this->isCsrfTokenValid('delete'.$companyUser->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($companyUser);
            $entityManager->flush();
        }

        return $this->redirectToRoute('company_users_index');
    }
}
