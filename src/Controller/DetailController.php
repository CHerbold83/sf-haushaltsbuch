<?php

namespace App\Controller;

use App\FinanceType;
use App\Entity\Finance;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use \DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DetailController extends AbstractController{

    public function __construct(private readonly EntityManagerInterface $em){

    }

    #[Route(path:"/detail/{date}", name:"detail", defaults:['date' => null], 
    methods: ['GET', 'HEAD'])]
    public function index($date){

        $monthlyIncome = $this->getFinance(true, FinanceType::Income, $date);
        $oneTimeIncome = $this->getFinance(false, FinanceType::Income, $date);
        $monthlyExpenses = $this->getFinance(true, FinanceType::Expenses, $date);
        $oneOffExpenses = $this->getFinance(false, FinanceType::Expenses, $date);
        $total = $this->getTotal($monthlyIncome, $oneTimeIncome, $monthlyExpenses, $oneOffExpenses);
        //both arrays are for view -> both must have the same key!
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

    /**
     * deletes the finance
     */
    #[Route(path:'/profile/delete_finance/{id}', methods: ['GET', 'DELETE'], name: 'delete_finance')]
    public function delete($id, Request $request): Response{
        $finance = $this->em->getRepository(Finance::class)->find($id);
        //checks if user is allowed to delete the finance
        if($finance->getUserId() != $this->getUser()->getId()){
            $this->addFlash('error','Sie haben keine Berechtigung diese Finanzen zu löschen!');
        } else {
            $this->em->remove($finance);
            $this->em->flush();
        }

        return $this->redirectToRoute('detail');
    }

    /**
     * gets the finance by date, monthly and type
     */
    public function getFinance(bool $monthly, FinanceType $type, $date):array{
        $newDate = new DateTime($date);
        $newDate->modify("first day of this month");
        $months = [];

        $repository = $this->em->getRepository(Finance::class);
        $entries = $repository->findBy(['user_id'=> $this->getUser()->getId(), 
                                        'monthly'=>$monthly,
                                        'type'=>$type]);

        foreach($entries as $entry){
            //checks if finance starts in the path for monthly
            //else: checks if it is the same month
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

    /**
     * gets total of all income and expenses
     */
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
}