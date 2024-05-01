<?php
session_start();


$currencyRates = array(
  "USD" => 1.0,
  "PH" => 54.43,
  "GBP" => 0.81,
  "EURO" => 0.92,
  "JPY" => 134.67,
  "AUD" => 1.39,
  "CAD" => 1.35,
  "CHF" => 0.92,
  "CNY" => 6.52,
  "NZD" => 1.52
);

function convertCurrency($amount, $fromCurrency, $toCurrency)
{
  global $currencyRates;

  if (!isset($currencyRates[$fromCurrency]) || !isset($currencyRates[$toCurrency])) {
    return "Invalid currency code";
  }

  $baseAmount = $amount / $currencyRates[$fromCurrency];
  $convertedAmount = $baseAmount * $currencyRates[$toCurrency];

  return number_format($convertedAmount, 2, '.', '');
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $amount = $_POST["amount"];
  $fromCurrency = $_POST["fromCurrency"];
  $toCurrency = $_POST["toCurrency"];

  if (!isset($_SESSION["attempts"])) {
    $_SESSION["attempts"] = 0;
  }

  $_SESSION["attempts"]++;

  if ($_SESSION["attempts"] > 4) {

    echo "<p class='remaining-attempts'>You have exceeded the maximum number of attempts. Please try again later.</p>";
    session_destroy();
    exit;
  }

  $result = convertCurrency($amount, $fromCurrency, $toCurrency);
}

?>

<!DOCTYPE html>
<html>

<head>
  <title>Currency Converter</title>
  <link rel="stylesheet" type="text/css" href="index.css">
</head>

<body>
  <div class="container">

    <?php
    // Maximum number of attempts
    $maxAttempts = 3;

    // Calculate remaining attempts
    $remainingAttempts = $maxAttempts - ($_SESSION["attempts"] ?? 0);

    // Display remaining attempts
    if ($remainingAttempts > 0) {
      echo "<p class='remaining-attempts'>You have $remainingAttempts tries left.</p>";
    } else if ($remainingAttempts === 0) {
      echo "<p class='remaining-attempts'>You have 0 tries left.</p>";
    } else {
      echo "<p>You have exceeded the maximum number of attempts. Please try again later.</p>";
      session_destroy();
      exit;
    }
    ?>
    <h1>Currency Converter</h1>
    <?php
    if (isset($result)) {
      $fromFlag = strtolower($fromCurrency) . ".png";
      $toFlag = strtolower($toCurrency) . ".png";
      echo "<p class='display'><img class='flag-icon' src='flags/$fromFlag'> $fromCurrency  $amount&nbsp  =  &nbsp<img class='flag-icon' src='flags/$toFlag'> $toCurrency $result</p>";
    }
    ?>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
      <label for="amount">Amount:</label>
      <input type="number" name="amount" id="amount" required><br>
      <div class="sub-container">
        <div class="container-child">
          <div class="currency-select">
            <label for="fromCurrency">From Currency:</label>
            <div class="for-img">
              <img id="from_flag" class="flag" src="flags/usd.png">
              <select id="from_currency" name="fromCurrency">
                <?php foreach ($currencyRates as $code_key => $rate) { ?>
                  <option value="<?php echo $code_key; ?>">
                    <?php echo $code_key; ?>
                  </option>
                <?php } ?>
              </select><br>
            </div>
          </div>
          <div class="currency-select">
            <label for="toCurrency">To Currency:</label>
            <div class="for-img">
              <img id="to_flag" class="flag" src="flags/usd.png">
              <select id="to_currency" name="toCurrency">
                <?php foreach ($currencyRates as $code_key => $rate) { ?>
                  <option value="<?php echo $code_key; ?>">
                    <?php echo $code_key; ?>
                  </option>
                <?php } ?>
              </select><br>
            </div>
          </div>
        </div>

        <div class="calculator">
          <button type="button" onclick="appendToInput('7')">7</button>
          <button type="button" onclick="appendToInput('8')">8</button>
          <button type="button" onclick="appendToInput('9')">9</button>
          <button type="button" onclick="appendToInput('4')">4</button>
          <button type="button" onclick="appendToInput('5')">5</button>
          <button type="button" onclick="appendToInput('6')">6</button>
          <button type="button" onclick="appendToInput('1')">1</button>
          <button type="button" onclick="appendToInput('2')">2</button>
          <button type="button" onclick="appendToInput('3')">3</button>
          <button type="button" onclick="appendToInput('0')">0</button>
          <button type="button" onclick="clearInput()">C</button>
        </div>
      </div>
      <br>
      <input type="submit" name="submit" value="Convert">
    </form>
    <script>
      function appendToInput(value) {
        document.getElementById("amount").value += value;
      }
      function clearInput() {
        document.getElementById("amount").value = "";
      }
      document.getElementById('from_currency').addEventListener('change', function () {
        var flag = this.value.toLowerCase() + ".png";
        document.getElementById('from_flag').src = "flags/" + flag;
      });
      document.getElementById('to_currency').addEventListener('change', function () {
        var flag = this.value.toLowerCase() + ".png";
        document.getElementById('to_flag').src = "flags/" + flag;
      });
    </script>
  </div>
</body>

</html>