<?php

namespace App\Form\Type;

use App\FinanceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;

class EditType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
            ->add('title', TextType::class)
            ->add('amount', NumberType::class)
            ->add('monthly', CheckboxType::class)
            ->add('type', EnumType::class, ['class' => FinanceType::class])
            ->add('date', DateType::class)
            ->add('save', SubmitType::class)
            ;
    }

}