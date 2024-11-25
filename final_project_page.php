<!--
C:\php\php.exe -S localhost:8080 -t D:\Git\CSE3241
http://localhost:8080/final_project_page.php
-->
<?php
$servername = "localhost";
$username = "root";
// $password = "mysql";
$password = "SQLpaiYUE=3.14";
$dbname = "finalproject";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Final Project</title>
    <script>
        function updateSliders(input, slider, label) {
            let total = parseFloat(document.getElementById("total_amount").value) || 0;
            let sliders = document.getElementsByClassName("slider");
            let allocated = 0;

            for (let s of sliders) {
                allocated += parseFloat(s.value);
            }

            let remaining = total - allocated + parseFloat(slider.value);
            slider.setAttribute('max', total);
            label.innerText = slider.value;
            input.value = slider.value; // Synchronize input box with slider

            if (allocated > total) {
                // alert("Total allocation exceeds total amount!");
                slider.value = parseFloat(slider.value) - (allocated - total); // Reset to valid value
                label.innerText = slider.value;
                input.value = slider.value; // Update input box
            }
        }
        function resetSliders() {
            let total = parseFloat(document.getElementById("total_amount").value) || 0;
            let sliders = document.getElementsByClassName("slider");

            for (let slider of sliders) {
                slider.setAttribute('max', total);
            }
        }

        function updateInput(input, slider, label) {
            let total = parseFloat(document.getElementById("total_amount").value) || 0;
            let moneyInputs = document.getElementsByClassName("money-input");
            let allocated = 0;

            for (let i of moneyInputs) {
                allocated += parseFloat(i.value);
            }

            let remaining = total - allocated + parseFloat(input.value);
            input.setAttribute('max', total);
            label.innerText = input.value;
            slider.value = input.value; // Synchronize

            if (allocated > total) {
                // alert("Total allocation exceeds total amount!");
                input.value = parseFloat(input.value) - (allocated - total); // Reset to valid value
                label.innerText = input.value;
                slider.value = input.value; // Update slider
            }
        }
            
        function resetInputs(totalAmountInput) {
            const totalAmount = parseFloat(totalAmountInput.value) || 0;
            const moneyInputs = document.querySelectorAll(".money-input");
            moneyInputs.forEach(input => {
                input.max = totalAmount; // Set max for each stock input to total amount
            });
        }
        function toggleDateRequirement(slider, buyDate, sellDate) {
            if (parseFloat(slider.value) > 0) {
                buyDate.required = true;
                sellDate.required = true;
                buyDate.value = ""; // Clear default value when required
                sellDate.value = ""; // Clear default value when required
            } else {
                buyDate.required = false;
                sellDate.required = false;
                buyDate.value = ""; // Set default date
                sellDate.value = ""; // Set default date
            }
        }

        function initializeSliderDateBinding() {
            const datePairs = document.querySelectorAll(".date-pair");
            datePairs.forEach(pair => {
                const slider = pair.querySelector(".slider");
                const buyDate = pair.querySelector(".buy-date");
                const sellDate = pair.querySelector(".sell-date");

                slider.addEventListener("input", () => toggleDateRequirement(slider, buyDate, sellDate));
            });
        }

        document.addEventListener("DOMContentLoaded", initializeSliderDateBinding);

    </script>
</head>
<body>
    <h1>Stock Profit/Loss Calculator</h1>
    <form action="" method="post">
        <label>Total Amount ($):</label>
        <input type="number" id="total_amount" name="total_amount" required oninput="resetSliders()"><br><br>

        <?php
        $stocks = ['AMZN', 'AAPL', 'GOOGL', 'META'];
        foreach ($stocks as $stock) {
            echo "<label>{$stock}:</label> 
                <input type='range' class='slider' name='{$stock}_allocation' min='0' value='0' 
                oninput='updateSliders(document.getElementById(\"{$stock}_input\"),  this, document.getElementById(\"label_{$stock}\"))'>
                <span id='label_{$stock}'>0</span><br>
                <input type='number' class='money-input'  id='{$stock}_input' min='0' value='0' style='width: 70px;' 
                oninput='updateInput(this, document.getElementsByName(\"{$stock}_allocation\")[0], document.getElementById(\"label_{$stock}\"))'>
                Buy Date: <input type='date' name='{$stock}_buy_date' min='2024-01-02' max='2024-09-30'> 
                Sell Date: <input type='date' name='{$stock}_sell_date' min='2024-01-02' max='2024-09-30'><br><br>";
        }
        ?>

        <button type="submit" name="calculate">Calculate</button>
    </form>

    <?php
    if (isset($_POST['calculate'])) {
        $total_amount = floatval($_POST['total_amount']);
        $brokerage_fee = 0.01;
        $irs_tax = 0.20;

        $total_profit = 0;

        foreach ($stocks as $stock) {
            $allocation = floatval($_POST["{$stock}_allocation"]);
            if ($allocation == 0) {
                continue;
            }
            $buy_date = $_POST["{$stock}_buy_date"];
            $sell_date = $_POST["{$stock}_sell_date"];
            // Validate dates
            if (empty($buy_date) || empty($sell_date)) {
                echo "<p>Error: Buy Date and Sell Date are required for $stock when allocation is greater than 0.</p>";
                continue;
            }
            
            if (strtotime($sell_date) < strtotime($buy_date)) {
                echo "<p>Error: sell date must be at least one day after the purchase
                    date for $stock.</p>";
                continue;
            }

            $buy_query = "SELECT stock_price FROM stock_data WHERE stock_label = '$stock' AND trading_date = '$buy_date'";
            $sell_query = "SELECT stock_price FROM stock_data WHERE stock_label = '$stock' AND trading_date = '$sell_date'";

            $buy_result = $conn->query($buy_query);
            $sell_result = $conn->query($sell_query);

            if ($buy_result->num_rows > 0 && $sell_result->num_rows > 0) {
                $buy_price = floatval($buy_result->fetch_assoc()['stock_price']);
                $sell_price = floatval($sell_result->fetch_assoc()['stock_price']);

                $investment = $total_amount * ($allocation / 100);
                $num_shares = $investment / $buy_price;

                $selling_price_net = $sell_price * (1 - $brokerage_fee) * $num_shares;
                $profit_before_tax = $selling_price_net - $investment;
                $tax_amount = $profit_before_tax > 0 ? $profit_before_tax * $irs_tax : 0;
                $profit_after_tax = $profit_before_tax - $tax_amount;

                $total_profit += $profit_after_tax;

                echo "<p>$stock: 
                    Bought at $buy_price, Sold at $sell_price, 
                    Net Profit (After Tax): $" . number_format($profit_after_tax, 2) . "</p>";
            } else {
                echo "<p>Error: Stock price data for $stock on selected dates is unavailable.</p>";
            }
        }

        echo "<h2>Total Profit/Loss: $" . number_format($total_profit, 2) . "</h2>";
    }
    $conn->close();
    ?>
</body>
</html>
