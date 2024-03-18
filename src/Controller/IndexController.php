<?php

namespace App\Controller;

use App\Entity\Finance;
use App\FinanceType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use \DateTime;
use Doctrine\ORM\EntityManagerInterface;

class IndexController extends AbstractController
{

    public function __construct(private readonly EntityManagerInterface $em){

    }

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        $months = [];
        $totalIncome = [];
        $totalExpenses = [];
        $date = new DateTime("midnight");
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

    public function getTotalByType(FinanceType $type, $date):float{
        $total = 0.0;
        $date->modify("first day of this month");
        $date->format("Y-m-01 00:00:00");

        $repository = $this->em->getRepository(Finance::class);
        $entries = $repository->findBy(['user_id'=> $this->getUser()->getId()]);
        foreach($entries as $entry){
            $entryDate = DateTime::createFromInterface($entry->getDate());
            if($entry->isMonthly()){
                if($entry->getType() == $type && $entryDate->getTimestamp() <= $date->getTimestamp()){
                    $total  += $entry->getAmount();
                }
            } else {
                if($entry->getType() == $type && $entryDate->getTimestamp() == $date->getTimestamp()){
                    $total  += $entry->getAmount();
                }
            }
        }
        return $total;
    }

    public function getTotalIncomeForMonth($date):float{
        return $this->getTotalByType(FinanceType::Income, $date);
    }

    public function getTotalExpensesForMonth($date): float{

        return $this->getTotalByType(FinanceType::Expenses, $date);
    }

    
}
