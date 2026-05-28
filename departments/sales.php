<?php
include "../includes/auth.php";
include "../includes/db.php";
include "../includes/header.php";
include "../includes/sidebar.php";

$message = "";

$department_id = $_SESSION['department_id'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;

if (!$department_id) {
    die("No department assigned.");
}

$stock = mysqli_query($conn, "
    SELECT
        department_stock.*,
        products.name AS product_name,
        products.unit,
        products.unit_price
    FROM department_stock
    LEFT JOIN products
    ON department_stock.product_id = products.id
    WHERE department_stock.department_id='$department_id'
    AND department_stock.quantity > 0
    ORDER BY products.name ASC
");

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    $note = mysqli_real_escape_string($conn, $_POST['note']);

    $check = mysqli_query($conn, "
        SELECT
            department_stock.*,
            products.unit_price,
            products.name AS product_name
        FROM department_stock
        LEFT JOIN products
        ON department_stock.product_id = products.id
        WHERE department_stock.department_id='$department_id'
        AND department_stock.product_id='$product_id'
        LIMIT 1
    ");

    $item = mysqli_fetch_assoc($check);

    if ($item && $quantity > 0) {

        $previous_quantity = intval($item['quantity']);

        if ($quantity <= $previous_quantity) {

            $new_quantity = $previous_quantity - $quantity;

            $unit_price = $item['unit_price'];
            $total_amount = $unit_price * $quantity;

            mysqli_query($conn, "
                UPDATE department_stock
                SET quantity='$new_quantity'
                WHERE department_id='$department_id'
                AND product_id='$product_id'
            ");

            mysqli_query($conn, "
                INSERT INTO department_sales (
                    department_id,
                    product_id,
                    quantity,
                    unit_price,
                    total_amount,
                    sold_by,
                    note
                ) VALUES (
                    '$department_id',
                    '$product_id',
                    '$quantity',
                    '$unit_price',
                    '$total_amount',
                    '$user_id',
                    '$note'
                )
            ");

            mysqli_query($conn, "
                INSERT INTO activity_logs (
                    user_id,
                    action,
                    details
                ) VALUES (
                    '$user_id',
                    'Department Sale',
                    'Sold $quantity of {$item['product_name']}'
                )
            ");

            $message = "Sale recorded successfully.";

        } else {

            $message = "Insufficient stock.";

        }

    } else {

        $message = "Invalid sale.";

    }
}
?>

<div class="md:ml-64 p-6">

<div class="bg-white rounded-2xl shadow-lg p-8 max-w-3xl">

<h1 class="text-3xl font-bold text-[#07152B] mb-6">
Department Sales
</h1>

<?php if($message): ?>

<div class="bg-blue-100 text-blue-700 p-3 rounded mb-5">
<?php echo $message; ?>
</div>

<?php endif; ?>

<form method="POST" class="space-y-5">

<select
name="product_id"
class="w-full border p-3 rounded-xl"
required
>

<option value="">
Select Product
</option>

<?php while($row=mysqli_fetch_assoc($stock)): ?>

<option value="<?php echo $row['product_id']; ?>">

<?php echo htmlspecialchars($row['product_name']); ?>

—
Available:
<?php echo $row['quantity']; ?>

<?php echo htmlspecialchars($row['unit']); ?>

—
₦<?php echo number_format($row['unit_price'],2); ?>

</option>

<?php endwhile; ?>

</select>

<input
type="number"
name="quantity"
placeholder="Quantity Sold"
class="w-full border p-3 rounded-xl"
required
>

<textarea
name="note"
placeholder="Sale note"
class="w-full border p-3 rounded-xl"
></textarea>

<button
class="bg-[#07152B] text-white px-6 py-3 rounded-xl w-full"
>
Record Sale
</button>

</form>

</div>

</div>

<?php include "../includes/footer.php"; ?>