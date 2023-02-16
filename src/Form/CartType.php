<?php

namespace App\Form;

use App\Entity\Order;
use App\EventSubscriber\ClearCartSubscriber;
use App\EventSubscriber\RemoveCartItemSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CartType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /*
        * items: a collection of CartItemType form type. It will allow us to modify all OrderItem items of an Order right inside the cart form itself,
        * save: a submit button to save the cart,
        * clear: a submit button to clear the cart.
        */
        $builder
            ->add('items', CollectionType::class, [
                'entry_type' => CartItemType::class
            ])
            ->add('save', SubmitType::class)
            ->add('clear', SubmitType::class)
        ;

        // Registering the Event Subscriber
        $builder->addEventSubscriber(new RemoveCartItemSubscriber());
        $builder->addEventSubscriber(new ClearCartSubscriber());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
            // enable/disable CSRF protection for this form
            'csrf_protection' => true,
            // the name of the hidden HTML field that stores the token
            'csrf_field_name' => '_token',
        ]);
    }
}
