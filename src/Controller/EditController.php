<?php

namespace App\Controller;

use App\Form\Type\EditType;
use App\Entity\Finance;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpFoundation\Request;

class EditController extends AbstractController{

    public function __construct(private readonly EntityManagerInterface $em){

    }

    /**
     * method to edit the finance
     */
    #[Route(path:"/edit/{id}", name:"edit", defaults:['id'=> null], 
    methods: ['GET', 'HEAD', 'POST'])]
    public function indexAction(Request $request, $id): Response{

        $finance = null;
        if($id){
            $finance = $this->getFinanceById($id);
            if($finance->getUserId() !== $this->getUser()->getId()){  
                return $this->redirectToRoute('index');
            }
        } else{
            $finance = new Finance();
        }

        $form = $this->createForm(EditType::class, $finance);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $finance = $form->getData();
            $finance->setUserId($this->getUser()->getId());
            $date = $finance->getDate();
            $date->modify("first day of this month");
            $finance->setDate($date);
            $this ->em->persist($finance);
            $this ->em->flush();
            return $this->redirectToRoute('index');
        }

        return $this->render('edit.html.twig', [
            'editForm'=>$form,
        ]);
    }

    /**
     * gets finance by id
     */
    public function getFinanceById($id): Finance{

        $repository = $this->em->getRepository(Finance::class);
        $financeForID = $repository->findBy(['id' => $id]);
        return $financeForID[0];
    }


}