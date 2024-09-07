<?php
$servername = "localhost";
$username = "sarj_user";
$password = "F@ckuB1tchSa";
$dbname = "sarj";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
die("Connection failed: " . $conn->connect_error);
}

$sql_company = "SELECT * FROM company";
$result_company = $conn->query($sql_company);

$sql_price_dc = "SELECT * FROM price WHERE Type='DC'";
$result_price_dc = $conn->query($sql_price_dc);

$sql_price_ac = "SELECT * FROM price WHERE Type='AC'";
$result_price_ac = $conn->query($sql_price_ac);

$prices_dc = [];
while ($row = $result_price_dc->fetch_assoc()) {
$prices_dc[$row['company_short_name']][] = $row;
}

$prices_ac = [];
while ($row = $result_price_ac->fetch_assoc()) {
$prices_ac[$row['company_short_name']][] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elektrikli araç şarj fiyatları</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>

<div class='inputs'>
    <div class='div-switch'>
        <div class='ac-dc'>AC</div>
        <label class="switch">
            <input type="checkbox" id="priceToggle" checked>
            <span class="slider round"></span>
        </label>
        <div class='ac-dc'>DC</div>
    </div>
<div class="filter-container">
    <input type="text" aria-placeholder='Firma...' id="company-filter" placeholder="Firma...">
</div>


</div>
<div class="table-container">
    <table>
        <thead>
        <tr>
            <th>Firma</th>
            <th>Fiyat (TL)</th>
        </tr>
        </thead>
        <tbody id="priceTable">
        <?php
            // Default to showing DC prices
            foreach ($result_company as $company) { ?>
        <tr>
            <td rowspan="<?php echo count($prices_dc[$company['shortname']]); ?>">
                <div class="logo-container">
                    <a target='_blank' href='<?php echo $company['price_page']; ?>'>
                    <img src="<?php echo $company['logo_path']; ?>" alt="<?php echo $company['shortname']; ?>">
                    </a>
                    <span><?php echo $company['Name']; ?></span>
                </div>
            </td>
            <?php
                    $first = true;

                    foreach($prices_dc[$company['shortname']] as $price) {
                        if (!$first) echo "<tr>";
            echo "<td>" . str_replace("{0}", number_format($price['Price'], 2), $price['template']) . "</td></tr>";
        $first = false;
        }
        ?>
        </tr>
        <?php } ?>
        </tbody>
    </table>
    <p style="text-align: center; font-size: 12px; color: #555; margin-top: 20px;">
        Bu tabloda yer alan tüm şirketler ve logolar yalnızca bilgilendirme amaçlıdır. Burada belirtilen firmalarla herhangi bir iş birliği veya bağlantımız bulunmamaktadır. Tüm fiyatlar ve bilgiler değişiklik gösterebilir; en güncel bilgi için ilgili firmanın resmi kaynaklarına başvurulmalıdır.
    </p>
</div>

<script>
    $(document).ready(function() {
        $('#priceToggle').on('change', function() {
            var isChecked = $(this).is(':checked');

            $.ajax({
                url: 'get_prices.php',
                type: 'POST',
                data: { type: isChecked ? 'DC' : 'AC' },
                success: function(response) {
                    $('#priceTable').html(response);
                }
            });
        });
    });
</script>
<script>
    $(document).ready(function() {
        // Filter table rows by company name
        $('#company-filter').on('keyup', function() {
            var filter = $(this).val().toUpperCase();
            $('#priceTable tr').each(function() {
                var companyName = $(this).find('td:eq(0) span').text().toUpperCase();
                if (companyName.indexOf(filter) > -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // Handle switch change (already in your script)
        $('#priceToggle').on('change', function() {
            var isChecked = $(this).is(':checked');

            $.ajax({
                url: 'get_prices.php',
                type: 'POST',
                data: { type: isChecked ? 'DC' : 'AC' },
                success: function(response) {
                    $('#priceTable').html(response);
                    // Reset the filter after switching between AC and DC
                    $('#company-filter').trigger('keyup');
                }
            });
        });
    });
</script>

</body>
</html>

<?php $conn->close(); ?>
