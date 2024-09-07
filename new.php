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

$sql_price = "SELECT * FROM price WHERE Type='DC'";
$result_price = $conn->query($sql_price);

$prices = [];
while ($row = $result_price->fetch_assoc()) {
$prices[$row['company_short_name']][] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elektrikli araç şarj fiyatları</title>
    <link rel="stylesheet" href="styles.css">

</head>
<body>
<div class='div-switch'>
    <div class='ac-dc'>
    AC
    </div>
<label class="switch">
    <input type="checkbox" checked>
    <span class="slider round"></span>
</label><div class='ac-dc'>
    DC
</div>
</div>
<div class="table-container">
    <table>
        <thead>
        <tr>
            <th>Firma</th>
            <th>DC (TL)</th>
        </tr>
        </thead>
        <tbody>
        <?php while($company = $result_company->fetch_assoc()) { ?>
        <tr>
            <td rowspan="<?php echo count($prices[$company['shortname']]); ?>">
                <div class="logo-container">
                    <a target='_blank' href='<?php echo $company['price_page']; ?>'>
                        <img src="<?php echo $company['logo_path']; ?>" alt="<?php echo $company['shortname']; ?>">
                    </a>
                    <span><?php echo $company['Name']; ?></span>
                </div>
            </td>
            <?php
                    $first = true;
                    foreach($prices[$company['shortname']] as $price) {
                        if (!$first) echo "<tr>";
            echo "<td>" . str_replace("{0}", $price['Price'], $price['template']) . "</td></tr>";
        $first = false;
        }
        ?>
        <?php } ?>
        </tbody>
    </table>
    <p style="text-align: center; font-size: 12px; color: #555; margin-top: 20px;">
        Bu tabloda yer alan tüm şirketler ve logolar yalnızca bilgilendirme amaçlıdır. Burada belirtilen firmalarla herhangi bir iş birliği veya bağlantımız bulunmamaktadır. Tüm fiyatlar ve bilgiler değişiklik gösterebilir; en güncel bilgi için ilgili firmanın resmi kaynaklarına başvurulmalıdır.
    </p>
</div>

</body>
</html>

<?php $conn->close(); ?>
