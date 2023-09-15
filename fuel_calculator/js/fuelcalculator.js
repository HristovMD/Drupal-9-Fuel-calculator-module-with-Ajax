(function($) {
  // Function to get query parameters from the URL.
  function getUrlParameter(name) {
      name = name.replace(/[\[\]]/g, "\\$&");
      var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
          results = regex.exec(window.location.href);
      if (!results) return null;
      if (!results[2]) return '';
      return decodeURIComponent(results[2].replace(/\+/g, " "));
  }

  // Get values from query parameters.
  var distanceParam = getUrlParameter('distance');
  var consumptionParam = getUrlParameter('consumption');
  var priceParam = getUrlParameter('price');

  // Populate form fields if query parameters are provided.
  if (distanceParam !== null) {
      document.getElementById('edit-distance').value = distanceParam;
  }

  if (consumptionParam !== null) {
      document.getElementById('edit-consumption').value = consumptionParam;
  }

  if (priceParam !== null) {
      document.getElementById('edit-price').value = priceParam;
  }

  // Function to validate form inputs.
  function validateInputs() {
      var distanceInput = document.getElementById('edit-distance').value;
      var consumptionInput = document.getElementById('edit-consumption').value;
      var priceInput = document.getElementById('edit-price').value;

      // Check for empty fields or non-numeric inputs.
      if (distanceInput === "" || isNaN(distanceInput) || consumptionInput === "" || isNaN(consumptionInput) || priceInput === "" || isNaN(priceInput)) {
          alert("Please enter valid numeric values for all fields before calculating.");
          return false;
      }

      return true;
  }

  // Attach a click event handler to the Calculate button.
  $('#edit-submit').click(function(event) {
      // Check form inputs before proceeding with the calculation.
      if (!validateInputs()) {
          event.preventDefault(); // Prevent form submission if validation fails.
      }
  });

})(jQuery);