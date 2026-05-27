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

$departments = mysqli_query($conn, "
    SELECT * FROM departments
    WHERE name != 'General Store'
    ORDER BY name ASC
");

if (!$products) {
    die("Products query failed: " . mysqli_error($conn));
}

if (!$departments) {
    die("Departments query failed: " . mysqli_error($conn));
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $product_id = intval($_POST['product_id']);
    $department_id = intval($_POST['department_id']);
    $quantity = intval($_POST['quantity']);
    $note = mysqli_real_escape_string($conn, $_POST['note']);
    $issued_by = $_SESSION['user_id'] ?? null;

    if (empty($issued_by)) {
        $message = "Session expired. Please log in again.";
    } else {

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

            if ($quantity <= $previous_quantity) {

                $new_quantity = $previous_quantity - $quantity;

                $update = mysqli_query($conn, "
                    UPDATE products
                    SET quantity='$new_quantity'
                    WHERE id='$product_id'
                ");

                if ($update) {

                    $issue = mysqli_query($conn, "
                        INSERT INTO stock_issues (
                            department_id,
                            issued_by,
                            status,
                            note
                        ) VALUES (
                            '$department_id',
                            '$issued_by',
                            'pending',
                            '$note'
                        )
                    ");

                    if ($issue) {

                        $stock_issue_id = mysqli_insert_id($conn);

                        $item_insert = mysqli_query($conn, "
                            INSERT INTO stock_issue_items (
                                stock_issue_id,
                                product_id,
                                quantity,
                                store_previous_quantity,
                                store_new_quantity
                            ) VALUES (
                                '$stock_issue_id',
                                '$product_id',
                                '$quantity',
                                '$previous_quantity',
                                '$new_quantity'
                            )
                        ");

                        if ($item_insert) {

                            $product_name = $product['name'];

                            $details = mysqli_real_escape_string(
                                $conn,
                                "Issued $quantity of $product_name to department. Awaiting confirmation."
                            );

                            mysqli_query($conn, "
                                INSERT INTO activity_logs (user_id, action, details)
                                VALUES (
                                    '$issued_by',
                                    'Issued Stock',
                                    '$details'
                                )
                            ");

                            $message = "Stock issued successfully. Awaiting department confirmation.";

                        } else {
                            $message = "Error saving issued item: " . mysqli_error($conn);
                        }

                    } else {
                        $message = "Error creating stock issue: " . mysqli_error($conn);
                    }

                } else {
                    $message = "Error updating store stock: " . mysqli_error($conn);
                }

            } else {
                $message = "Insufficient store stock.";
            }

        } else {
            $message = "Invalid product or quantity.";
        }
    }
}
?>

<div class="md:ml-64 p-6">

    <div class="bg-white p-8 rounded-2xl shadow-lg max-w-3xl">

        <h1 class="text-2xl font-bold mb-6 text-[#07152B]">
            Issue Stock to Department
        </h1>

        <?php if ($message): ?>
            <div class="bg-blue-100 text-blue-700 p-3 rounded mb-5">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-5">

            <select name="product_id" class="w-full border p-3 rounded-xl" required>
                <option value="">Select Product</option>

                <?php while ($row = mysqli_fetch_assoc($products)): ?>
                    <option value="<?php echo $row['id']; ?>">
                        <?php echo htmlspecialchars($row['name']); ?>
                        — Store Qty: <?php echo (int)$row['quantity']; ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <select name="department_id" class="w-full border p-3 rounded-xl" required>
                <option value="">Select Department</option>

                <?php while ($dept = mysqli_fetch_assoc($departments)): ?>
                    <option value="<?php echo $dept['id']; ?>">
                        <?php echo htmlspecialchars($dept['name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <input
                type="number"
                name="quantity"
                placeholder="Quantity to issue"
                class="w-full border p-3 rounded-xl"
                required
            >

            <textarea
                name="note"
                placeholder="Purpose / note"
                class="w-full border p-3 rounded-xl"
            ></textarea>

            <button class="bg-[#07152B] text-white px-6 py-3 rounded-xl w-full">
                Issue Stock
            </button>

        </form>

    </div>

</div>

<?php include "../includes/footer.php"; ?>