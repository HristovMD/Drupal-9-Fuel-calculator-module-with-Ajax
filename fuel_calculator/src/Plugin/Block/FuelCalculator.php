<?php
/**
 * @file
 * Contains \Drupal\fuelcalculator\Plugin\Block\FuelCalculatorBlock.
 */
namespace Drupal\fuel_calculator\Plugin\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'fuelcalculator' block.
 *
 * @Block(
 *   id = "fuel_calculator_block",
 *   admin_label = @Translation("Fuel Calculator"),
 *   category = @Translation("Fuelcalculator")
 * )
 */
class Fuelcalculator extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
    $form = \Drupal::formBuilder()->getForm('Drupal\fuel_calculator\Form\FuelCalculatorForm');
    return $form;
   }
}