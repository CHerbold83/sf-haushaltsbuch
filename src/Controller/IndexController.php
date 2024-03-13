<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use \DateTime;

class IndexController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        $months = [];
        $totalIncome = [];
        $totalExpenses = [];
        $date = new \DateTime();
        $date -> format('M Y');

        for($i = 0; $i < 12; $i++){
            $months[] = $date -> format('M Y');
            $totalIncome[$date->format('M Y')] = $this->getTotalIncomeForMonth($date);
            $totalExpenses[$date->format('M Y')] = $this->getTotalExpensesForMonth($date);
            $total[$date->format('M Y')] = $this->getTotalIncomeForMonth($date) - $this->getTotalExpensesForMonth($date);
            $date -> modify('first day of next month');

        }

        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
            'title' => 'Startseite',
            'months' => $months,
            'totalIncome' => $totalIncome,
            'totalExpenses' => $totalExpenses,
            'total' => $total,
        ]);
    }

    public function getTotalIncomeForMonth($date):float{

        $total = 0.0;

        return $total;
    }

    public function getTotalExpensesForMonth($date): float{

        $total = 0.0;

        return $total;
    }
}
