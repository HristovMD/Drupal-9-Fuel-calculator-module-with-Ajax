<?php
namespace Drupal\fuel_calculator\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Url;

class FuelCalculatorForm extends FormBase
{
    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return "fuel_calculator_form";
    }
    protected $messenger;

    public function __construct(MessengerInterface $messenger)
    {
        $this->messenger = $messenger;
    }

    public static function create(ContainerInterface $container)
    {
        return new static($container->get("messenger"));
    }
    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $form["#attached"]["library"][] = "fuel_calculator/fuel-calculator-js";

        $form["distance"] = [
            "#type" => "textfield",
            "#title" => t("Distance travelled:"),
            "#size" => 10,
            "#maxlength" => 64,
            // "#required" => true,
            "#prefix" => '<div id="distance">',
            "#suffix" => "</div>",
        ];

        $form["consumption"] = [
            "#type" => "textfield",
            "#title" => t("Fuel consumption:"),
            "#size" => 10,
            "#maxlength" => 64,
            // "#required" => true,
            "#prefix" => '<div id="consumption">',
            "#suffix" => "</div>",
        ];

        $form["price"] = [
            "#type" => "textfield",
            "#title" => t("Price per liter:"),
            "#size" => 10,
            "#maxlength" => 64,
            // "#required" => true,
            "#prefix" => '<div id="price">',
            "#suffix" => "</div>",
        ];

        $form["calculate"] = [
            "#type" => "submit",
            "#value" => t("Calculate"),
            "#ajax" => [
                "callback" => [$this, "calculateAjaxCallback"],
                "event" => "click",
            ],
        ];

        // Display the calculated values if available.
        $form["result1"]["#value"] = $form_state->get("result1") ?? "";
        $form["result2"]["#value"] = $form_state->get("result2") ?? "";

        return $form;
    }
    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
    }

    /**
     * Ajax callback for the "Calculate" button.
     */
    public function calculateAjaxCallback(
        array &$form,
        FormStateInterface $form_state
    ) {
        $response = new AjaxResponse();

        // Check if values are not empty and numeric.
        $distance = $form_state->getValue("distance");
        $consumption = $form_state->getValue("consumption");
        $price = $form_state->getValue("price");

        if (empty($distance) || !is_numeric($distance) || $distance <= 0) {
            $form_state->setErrorByName(
                "distance",
                $this->t("Please enter a valid numeric value for Distance.")
            );
        }

        if (
            empty($consumption) ||
            !is_numeric($consumption) ||
            $consumption <= 0
        ) {
            $form_state->setErrorByName(
                "consumption",
                $this->t(
                    "Please enter a valid numeric value for Fuel consumption."
                )
            );
        }

        if (empty($price) || !is_numeric($price) || $price <= 0) {
            $form_state->setErrorByName(
                "price",
                $this->t(
                    "Please enter a valid numeric value for Price per liter."
                )
            );
        }

        // Check if there are any form errors.
        if ($form_state->hasAnyErrors()) {
            // Return the form with error messages.
            $response->addCommand(
                new ReplaceCommand("#" . $form["#id"], $form)
            );
        } else {
            // Perform calculations.
            $fuelSpent = ($distance * $consumption) / 100;
            $fuelCost = $fuelSpent * $price;

            // Store the calculated values in the form state.
            $form_state->setStorage([
                "result1" => $fuelSpent,
                "result2" => $fuelCost,
            ]);

            // Display messages using the messenger service.
            $this->messenger->addMessage("Fuel Spent: " . $fuelSpent);
            $this->messenger->addMessage("Fuel Cost: " . $fuelCost);

            // Refresh the page.
            $response->addCommand(
                new RedirectCommand(Url::fromUri("internal:/"))
            );
        }

        return $response;
    }
}
