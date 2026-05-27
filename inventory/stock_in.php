<?php
include "../includes/auth.php";
include "../includes/db.php";
include "../includes/header.php";
include "../includes/sidebar.php";

$message = "";

$products = mysqli_query($conn, "
    SELECT * FROM products
    WHERE status='active'
    ORDER BY name ASC
");

if (!$products) {
    die("Products query failed: " . mysqli_error($conn));
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    $note = mysqli_real_escape_string($conn, $_POST['note']);
    $user_id = $_SESSION['user_id'] ?? null;

    $product_query = mysqli_query($conn, "
        SELECT * FROM products
        WHERE id='$product_id'
        LIMIT 1
    ");

    if (!$product_query) {
        die("Product lookup failed: " . mysqli_error($conn));
    }

    $product = mysqli_fetch_assoc($product_query);

    if ($product && $quantity > 0) {

        $previous_quantity = intval($product['quantity']);
        $new_quantity = $previous_quantity + $quantity;

        $update = mysqli_query($conn, "
            UPDATE products
            SET quantity='$new_quantity'
            WHERE id='$product_id'
        ");

        if ($update) {

            if (!empty($user_id)) {
                $product_name = $product['name'];

                $details = mysqli_real_escape_string(
                    $conn,
                    "Added $quantity to $product_name. Previous: $previous_quantity, New: $new_quantity. $note"
                );

                mysqli_query($conn, "
                    INSERT INTO activity_logs (user_id, action, details)
                    VALUES (
                        '$user_id',
                        'Stock In',
                        '$details'
                    )
                ");
            }

            $message = "Stock added successfully.";

        } else {
            $message = "Error updating stock: " . mysqli_error($conn);
        }

    } else {
        $message = "Invalid product or quantity.";
    }
}
?>

<div class="md:ml-64 p-6">

    <div class="bg-white p-8 rounded-2xl shadow-lg max-w-3xl">

        <h1 class="text-2xl font-bold mb-6 text-[#07152B]">
            Stock In - General Store
        </h1>

        <?php if ($message): ?>
            <div class="bg-green-100 text-green-700 p-3 rounded mb-5">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-5">

            <select name="product_id" class="w-full border p-3 rounded-xl" required>
                <option value="">Select Product</option>

                <?php while ($row = mysqli_fetch_assoc($products)): ?>
                    <option value="<?php echo $row['id']; ?>">
                        <?php echo htmlspecialchars($row['name']); ?>
                        — Current Qty: <?php echo (int)$row['quantity']; ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <input
                type="number"
                name="quantity"
                placeholder="Quantity received into store"
                class="w-full border p-3 rounded-xl"
                required
            >

            <textarea
                name="note"
                placeholder="Note e.g. supplied by vendor"
                class="w-full border p-3 rounded-xl"
            ></textarea>

            <button class="bg-[#07152B] text-white px-6 py-3 rounded-xl w-full">
                Add Stock
            </button>

        </form>

    </div>

</div>

<?php include "../includes/footer.php"; ?>