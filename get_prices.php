<?php
$servername = "localhost";
$username = "sarj_user";
$password = "F@ckuB1tchSa";
$dbname = "sarj";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
die("Connection failed: " . $conn->connect_error);
}

$type = $_POST['type'];

$sql_company = "SELECT * FROM company";
$result_company = $conn->query($sql_company);

$sql_price = "SELECT * FROM price WHERE Type='$type'";
$result_price = $conn->query($sql_price);

$prices = [];
while ($row = $result_price->fetch_assoc()) {
$prices[$row['company_short_name']][] = $row;
}

foreach ($result_company as $company) { ?>
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
    echo "<td>" . str_replace("{0}", number_format($price['Price'], 2), $price['template']) . "</td></tr>";
$first = false;
}
?>
</tr>
<?php }

$conn->close();
?>
