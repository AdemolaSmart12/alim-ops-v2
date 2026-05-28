<?php
include "../includes/auth.php";
include "../includes/db.php";
include "../includes/header.php";
include "../includes/sidebar.php";

$role = $_SESSION['role'] ?? '';
$department_id = $_SESSION['department_id'] ?? null;

if ($role == "department_user") {

    $usage = mysqli_query($conn,"
        SELECT
        stock_usage.*,
        products.name AS product_name,
        departments.name AS department_name,
        users.name AS staff_name

        FROM stock_usage

        LEFT JOIN products
        ON stock_usage.product_id=products.id

        LEFT JOIN departments
        ON stock_usage.department_id=departments.id

        LEFT JOIN users
        ON stock_usage.used_by=users.id

        WHERE stock_usage.department_id='$department_id'

        ORDER BY stock_usage.id DESC
    ");

} else {

    $usage = mysqli_query($conn,"
        SELECT
        stock_usage.*,
        products.name AS product_name,
        departments.name AS department_name,
        users.name AS staff_name

        FROM stock_usage

        LEFT JOIN products
        ON stock_usage.product_id=products.id

        LEFT JOIN departments
        ON stock_usage.department_id=departments.id

        LEFT JOIN users
        ON stock_usage.used_by=users.id

        ORDER BY stock_usage.id DESC
    ");
}
?>

<div class="md:ml-64 p-6">

<div class="bg-white rounded-2xl shadow-lg p-6">

<h1 class="text-3xl font-bold mb-6">
Usage History
</h1>

<div class="overflow-x-auto">

<table class="w-full">

<thead>

<tr class="bg-slate-100">

<th class="p-3">Department</th>
<th class="p-3">Item</th>
<th class="p-3">Quantity</th>
<th class="p-3">Used By</th>
<th class="p-3">Note</th>
<th class="p-3">Date</th>

</tr>

</thead>

<tbody>

<?php while($row=mysqli_fetch_assoc($usage)): ?>

<tr class="border-b">

<td class="p-3">
<?php echo $row['department_name']; ?>
</td>

<td class="p-3">
<?php echo $row['product_name']; ?>
</td>

<td class="p-3">
<?php echo $row['quantity']; ?>
</td>

<td class="p-3">
<?php echo $row['staff_name']; ?>
</td>

<td class="p-3">
<?php echo $row['note']; ?>
</td>

<td class="p-3">
<?php echo date(
"d M Y h:i A",
strtotime($row['created_at'])
);?>
</td>

</tr>

<?php endwhile; ?>

</tbody>

</table>

</div>

</div>

</div>

<?php include "../includes/footer.php"; ?>