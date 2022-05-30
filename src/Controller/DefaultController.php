<?php

namespace App\Controller;

use App\Entity\Members;

use App\Form\AddMemberType;
use App\Repository\MembersRepository;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route('/accueil', name: 'app_default')]
    public function index(Request $request, EntityManagerInterface $entityManager, MembersRepository $membersRepository): Response
    {

        $message = null;

        $member = new Members();
        $form = $this->createForm(AddMemberType::class, $member);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $member = $form->getData();
            $count = $membersRepository->countAll();
            if ($count >= '50') {
                $message = 'Vous ne pouvez plus vous inscrire ';
            } else {
                $entityManager->persist($member);
                $entityManager->flush();
                return $this->redirect($request->getUri());
            }

        }


        return $this->render('default/index.html.twig', [
            'form' => $form->createView(),
            'members' => $membersRepository->findAll(),
            'message' => $message
        ]);
    }
}
