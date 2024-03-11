<?php

namespace App\Form\Type;

use App\Entity\UserEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoginType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder->add("email", EmailType::class, ['empty_data'=>'']);
        $builder->add("password", PasswordType::class, ['empty_data'=>'']);
        $builder->add("submit", SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults([
            'data_class'=>UserEntity::class,
        ]);
    }
}