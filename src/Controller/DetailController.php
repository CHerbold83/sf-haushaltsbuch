<?php

namespace App\Controller;

use App\FinanceType;
use App\Entity\Finance;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use \DateTime;
use Doctrine\ORM\EntityManagerInterface;

class DetailController extends AbstractController{

    public function __construct(private readonly EntityManagerInterface $em){

    }

    #[Route(path:"/detail/{date}/{id}", name:"detail", defaults:['date' => null, 'id'=> null], 
    methods: ['GET', 'HEAD'])]
    public function index($date, $id){

        if($id !== null){
            $this->deleteFinanceFromDatabase($id);
        }

        $monthlyIncome = $this->getFinance(true, FinanceType::Income, $date);
        $oneTimeIncome = $this->getFinance(false, FinanceType::Income, $date);
        $monthlyExpenses = $this->getFinance(true, FinanceType::Expenses, $date);
        $oneOffExpenses = $this->getFinance(false, FinanceType::Expenses, $date);
        $total = $this->getTotal($monthlyIncome, $oneTimeIncome, $monthlyExpenses, $oneOffExpenses);
        $finances = [
            "monthlyIncome"=>$monthlyIncome,
            "oneTimeIncome"=>$oneTimeIncome,
            "monthlyExpenses"=>$monthlyExpenses,
            "oneOffExpenses"=>$oneOffExpenses
        ];
        $types= array(
            "monthlyIncome"=>"Monatliches Einkommen",
            "oneTimeIncome"=>"Einmaliges Einkommen",
            "monthlyExpenses"=>"Monatliche Ausgaben",
            "oneOffExpenses"=>"Einmalige Ausgaben"
        );

        return $this->render('detail.html.twig', [
            "finances"=>$finances,
            "types"=>$types,
            "date"=> $date,
            "total"=> $total,
        ]);
    }

    public function getFinance(bool $monthly, FinanceType $type, $date):array{
        $newDate = new DateTime($date);
        $newDate->modify("first day of this month");
        $months = [];

        $repository = $this->em->getRepository(Finance::class);
        $entries = $repository->findBy(['user_id'=> $this->getUser()->getId(), 
                                        'monthly'=>$monthly,
                                        'type'=>$type]);

        foreach($entries as $entry){
            $entityDate = DateTime::createFromInterface($entry->getDate());
            if($entry->isMonthly()){
                if($entityDate->getTimestamp() <= $newDate->getTimestamp()){
                    $months[] = $entry;
                }
            } else {
                if($entityDate->format('Y-m-d') == $newDate->format('Y-m-d')){
                    $months[] = $entry;
                }
            }
        }
        return $months;
    }

    public function getTotal($mIncome, $oIncome, $mExpenses, $oExpenses):float{
        $total = 0.0;
        foreach($mIncome as $income){
            $total += (double)$income->getAmount();
        }
        foreach($oIncome as $income){
            $total += $income->getAmount();
        }
        foreach($mExpenses as $expense){
            $total -= $expense->getAmount();
        }
        foreach($oExpenses as $expense){
            $total -= $expense->getAmount();
        }
        return $total;
    }

    public function deleteFinanceFromDatabase($id) {
        $finance = $this->em->getRepository(Finance::class)->find($id);
        if($finance->getUserId() != $this->getUser()->getId()){
            $this->addFlash('error','Sie haben keine Berechtigung diese Finanzen zu löschen!');
        } else {
            $this->em->remove($finance);
            $this->em->flush();
        }
    }
}