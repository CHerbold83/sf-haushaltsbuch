<?php

namespace App\Controller;

use App\Form\Type\UserType;
use App\Entity\Finance;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;

class ProfileController extends AbstractController{

    public function __construct(private readonly EntityManagerInterface $em){

    }

    /**
     * shows the profile of the user
     * email is editable
     */
    #[Route(path:"/profile", name:"profile", methods: ['POST', 'GET'])]
    public function indexAction(Request $request): Response{

        $user = $this->getUser();

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $user = $form->getData();
            $this ->em->persist($user);
            $this ->em->flush();
            return $this->redirectToRoute('index');
        }

        return $this->render('profile.html.twig', [
            'profileForm'=>$form,
        ]);
    }

    /**
     * deletes the user if wanted
     * also deletes all finances for the user
     */
    #[Route(path:'/profile/delete_user/{id}', methods: ['GET', 'DELETE'], name: 'delete_user')]
    public function delete($id, Request $request): Response{
        $user = $this->getUser();
        $session = $request->getSession();
        $session = new Session();
        $session->invalidate();
        //delete all finances for user
        $repository = $this->em->getRepository(Finance::class);
        $finances = $repository->findBy(['user_id'=> $this->getUser()->getId()]);
        foreach($finances as $finance){
            $this->em->remove($finance);
        }
        $this->em->remove($user);
        $this ->em->flush();

        return $this->redirectToRoute('login');
    }
}