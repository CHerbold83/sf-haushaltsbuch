<?php

namespace App\Controller;

use App\Form\Type\LoginType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController{

    #[Route(path:"/", name:"login")]
    public function indexAction(): Response{
        $form = $this->createForm(LoginType::class);
        return $this->render("login.html.twig", [
            'loginForm'=>$form
        ]);
    }
}