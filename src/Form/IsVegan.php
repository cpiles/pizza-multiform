<?php

namespace App\Form;

use Habitissimo\MultiForm\Form\Contract\Invokable;

class IsVegan implements Invokable
{
  public function __invoke($args): bool
  {
    return $args['vegetarian']['vegan'];
  }
}
