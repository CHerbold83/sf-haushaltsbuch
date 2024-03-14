<?php

namespace App\Form\Type;

use App\FinanceType;
use App\Entity\Finance;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
            ->add('title', TextType::class, ['label'=>'Titel'])
            ->add('amount', NumberType::class, ['label'=>'Betrag'])
            ->add('monthly', CheckboxType::class, ['label'=>'Monatlich'])
            ->add('type', EnumType::class, ['class' => FinanceType::class, 'label'=>'Typ'])
            ->add('date', DateType::class, ['label'=>'Fälligkeitsdatum'])
            ->add('save', SubmitType::class, ['label'=>'Speichern'])
            ;
    }

    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults([
            'data_class'=> Finance::class,
            'csrf_protection'=> true,
        ]);
    }

}