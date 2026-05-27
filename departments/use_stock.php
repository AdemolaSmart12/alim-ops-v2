<?php
include "../includes/auth.php";
include "../includes/db.php";
include "../includes/header.php";
include "../includes/sidebar.php";

$message = "";

$department_id = $_SESSION['department_id'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;

if (!$department_id) {
    die("No department assigned to this user.");
}

$stock = mysqli_query($conn, "
    SELECT
        department_stock.*,
        products.name AS product_name,
        products.unit
    FROM department_stock
    LEFT JOIN products ON department_stock.product_id = products.id
    WHERE department_stock.department_id='$department_id'
    AND department_stock.quantity > 0
    ORDER BY products.name ASC
");

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    $note = mysqli_real_escape_string($conn, $_POST['note']);

    $check = mysqli_query($conn, "
        SELECT *
        FROM department_stock
        WHERE department_id='$department_id'
        AND product_id='$product_id'
        LIMIT 1
    ");

    $item = mysqli_fetch_assoc($check);

    if ($item && $quantity > 0) {

        $previous_quantity = intval($item['quantity']);

        if ($quantity <= $previous_quantity) {

            $new_quantity = $previous_quantity - $quantity;

            mysqli_query($conn, "
                UPDATE department_stock
                SET quantity='$new_quantity'
                WHERE department_id='$department_id'
                AND product_id='$product_id'
            ");

            mysqli_query($conn, "
                INSERT INTO stock_usage (
                    department_id,
                    product_id,
                    quantity,
                    note,
                    used_by
                ) VALUES (
                    '$department_id',
                    '$product_id',
                    '$quantity',
                    '$note',
                    '$user_id'
                )
            ");

            $message = "Stock usage recorded successfully.";

        } else {
            $message = "Insufficient department stock.";
        }

    } else {
        $message = "Invalid product or quantity.";
    }
}
?>

<div class="md:ml-64 p-6">

    <div class="bg-white p-8 rounded-2xl shadow-lg max-w-3xl">

        <h1 class="text-2xl font-bold mb-6 text-[#07152B]">
            Use / Consume Department Stock
        </h1>

        <?php if ($message): ?>
            <div class="bg-blue-100 text-blue-700 p-3 rounded mb-5">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-5">

            <select name="product_id" class="w-full border p-3 rounded-xl" required>
                <option value="">Select Item</option>

                <?php while ($row = mysqli_fetch_assoc($stock)): ?>
                    <option value="<?php echo $row['product_id']; ?>">
                        <?php echo htmlspecialchars($row['product_name']); ?>
                        — Available:
                        <?php echo (int)$row['quantity']; ?>
                        <?php echo htmlspecialchars($row['unit']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <input
                type="number"
                name="quantity"
                placeholder="Quantity used / sold"
                class="w-full border p-3 rounded-xl"
                required
            >

            <textarea
                name="note"
                placeholder="Note e.g. sold at bar, used for room cleaning, kitchen production"
                class="w-full border p-3 rounded-xl"
            ></textarea>

            <button class="bg-[#07152B] text-white px-6 py-3 rounded-xl w-full">
                Record Usage
            </button>

        </form>

    </div>

</div>

<?php include "../includes/footer.php"; ?>