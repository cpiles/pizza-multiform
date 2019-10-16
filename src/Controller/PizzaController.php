<?php

declare(strict_types = 1);

namespace App\Controller;

use App\Form\CarnivoreType;
use App\Form\CheeseType;
use App\Form\IsVegan;
use App\Form\IsVegetarianPizza;
use App\Form\PizzaTimeType;
use App\Form\PizzaType;
use App\Form\VegetarianType;
use Habitissimo\MultiForm\Form\Entity\Director;
use Habitissimo\MultiForm\Form\MultiForm;
use Habitissimo\MultiForm\Form\MultiFormRenderer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class PizzaController extends AbstractController
{
  public function order(Request $request)
  {
    $vegetarian_director = new Director('vegetarian', 'carnivore', new IsVegetarianPizza());
    $vegan_director = new Director('time', 'cheese', new IsVegan());

    $form = new MultiForm('you-need-a-pizza', 'test_key');

    $form
      ->addStep('pizza', PizzaType::class, $vegetarian_director)
      ->addStep('vegetarian', VegetarianType::class, $vegan_director)
      ->addStep('carnivore', CarnivoreType::class, 'cheese')
      ->addStep('cheese', CheeseType::class, 'time')
      ->addStep('time', PizzaTimeType::class, null)
      ->setInitialStep('pizza');

    if ($request->isMethod('POST')) {
      $form->handleRequest();
      if ($form->isComplete()) {
        return $this->redirectToRoute('app_pizza_number', ['pizza' => $form->data()]);
      }
    }

    $renderer = new MultiFormRenderer($form);
    $renderer
      ->addView('pizza', 'PizzaForm.twig', ['track_event' => 'pizza_form_event'])
      ->addView('vegetarian', 'VegetarianForm.twig')
      ->addView('carnivore', 'CarnivoreForm.twig')
      ->addView('cheese', 'CheeseForm.twig')
      ->addView('time', 'TimeForm.twig');

    return $this->render('pizza/order.html.twig', [
      'form' => $renderer->render('es'),
    ]);
  }

  public function number(Request $request)
  {
    $pizza = $request->get('pizza');
    $info = [
      'name' => $pizza['pizza']['name'],
      'type' => $pizza['pizza']['type'],
      'pineapple' => $pizza[$pizza['pizza']['type']]['mushroom'] ?? null,
      'onion' => $pizza[$pizza['pizza']['type']]['mushroom'] ?? null,
      'mushroom' => $pizza[$pizza['pizza']['type']]['mushroom'] ?? null,
      'bacon' => $pizza[$pizza['pizza']['type']]['bacon'] ?? null,
      'beef' => $pizza[$pizza['pizza']['type']]['beef'] ?? null,
      'peperoni' => $pizza[$pizza['pizza']['type']]['peperoni'] ?? null,
      'cheese' => isset($pizza['cheese']) ? $pizza['cheese']['cheese'] : null,
      'time' => $pizza['time']['time'],
    ];

    return $this->render('pizza/number.html.twig', [
      'number' => mt_rand(1, 30),
      'info' => $info,
    ]);
  }
}