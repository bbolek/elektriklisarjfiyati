<?php
$servername = "localhost";
$username = "u194282610_sarj_istasyon";
$password = "YG~%g.k.@4E~2FH";
$dbname = "u194282610_sarj_comp";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
die("Connection failed: " . $conn->connect_error);
}

$sql_company = "SELECT * FROM company order by name asc";
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
    <meta name="description" content="Türkiye'deki farklı elektrikli araç şarj istasyonlarının fiyatlarını karşılaştırın. En uygun şarj fiyatlarını bulun ve bütçenizi koruyun.">
    <meta name="keywords" content="şarj istasyonu fiyat karşılaştırma, elektrikli araç, şarj fiyatları, ucuz şarj noktaları, elektrikli araç şarj istasyonu, Trugo, astor, aksa, beeful, eşarj, epower, enyakıt, ecobox, gio, Lumicle, miggo, Oncharge, Öniz, onlife, Otojet, ovolt, Şarjon, sharz, shell, solarşarj, tunçmatik, voltrun, wat, zes">
    <meta name="author" content="Şarj Fiyat">
    <meta property="og:title" content="Elektrikli Araç Şarj İstasyonu Fiyat Karşılaştırması | ŞarjFiyat">
    <meta property="og:description" content="Farklı şarj istasyonlarının fiyatlarını karşılaştırın ve en uygun şarj noktasını bulun. Güncel şarj fiyatlarıyla elektrikli araç sahiplerine rehberlik edin.">
    <meta property="og:url" content="https://www.sarjfiyat.com">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Elektrikli Araç Şarj İstasyonu Fiyat Karşılaştırması | ŞarjFiyat">
    <meta name="twitter:description" content="Farklı şarj istasyonlarının fiyatlarını karşılaştırın ve en uygun şarj noktasını bulun. Güncel şarj fiyatlarıyla elektrikli araç sahiplerine rehberlik edin.">
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
            var showRow = false;

            $('#priceTable tr').each(function() {
                var companyName = $(this).find('td:eq(0) span').text().toUpperCase();

                // If the current row contains a company name
                if (companyName) {
                    if (companyName.indexOf(filter) > -1) {
                        showRow = true; // Show this row and related rows
                    } else {
                        showRow = false; // Hide this row and related rows
                    }
                }

                // Show or hide the row based on the filter match
                if (showRow) {
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
