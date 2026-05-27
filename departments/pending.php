<?php
include "../includes/auth.php";
include "../includes/db.php";
include "../includes/header.php";
include "../includes/sidebar.php";

$message = "";

$department_id = $_SESSION['department_id'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;

if (!$department_id) {
    echo "
    <div class='md:ml-64 p-6'>
        <div class='bg-red-100 text-red-700 p-5 rounded-xl'>
            No department assigned to this user.
        </div>
    </div>";

    include "../includes/footer.php";
    exit();
}

if (isset($_GET['receive'])) {

    $issue_id = intval($_GET['receive']);

    $issue = mysqli_query($conn, "
        SELECT *
        FROM stock_issues
        WHERE id='$issue_id'
        AND department_id='$department_id'
        AND status='pending'
        LIMIT 1
    ");

    if ($issue && mysqli_num_rows($issue) == 1) {

        $items = mysqli_query($conn, "
            SELECT *
            FROM stock_issue_items
            WHERE stock_issue_id='$issue_id'
        ");

        while ($item = mysqli_fetch_assoc($items)) {

            $product_id = intval($item['product_id']);
            $quantity = intval($item['quantity']);

            $check = mysqli_query($conn, "
                SELECT *
                FROM department_stock
                WHERE department_id='$department_id'
                AND product_id='$product_id'
                LIMIT 1
            ");

            if ($check && mysqli_num_rows($check) > 0) {

                $stock = mysqli_fetch_assoc($check);
                $previous = intval($stock['quantity']);
                $new = $previous + $quantity;

                mysqli_query($conn, "
                    UPDATE department_stock
                    SET quantity='$new'
                    WHERE department_id='$department_id'
                    AND product_id='$product_id'
                ");

            } else {

                $previous = 0;
                $new = $quantity;

                mysqli_query($conn, "
                    INSERT INTO department_stock (
                        department_id,
                        product_id,
                        quantity
                    ) VALUES (
                        '$department_id',
                        '$product_id',
                        '$new'
                    )
                ");
            }

            mysqli_query($conn, "
                UPDATE stock_issue_items
                SET
                    department_previous_quantity='$previous',
                    department_new_quantity='$new'
                WHERE id='{$item['id']}'
            ");
        }

        mysqli_query($conn, "
            UPDATE stock_issues
            SET
                status='received',
                received_by='$user_id',
                received_at=NOW()
            WHERE id='$issue_id'
        ");

        mysqli_query($conn, "
            INSERT INTO activity_logs (user_id, action, details)
            VALUES (
                '$user_id',
                'Received Stock',
                'Department confirmed receipt for issue ID $issue_id'
            )
        ");

        $message = "Stock received successfully.";

    } else {
        $message = "Transfer not found or already received.";
    }
}

$pending = mysqli_query($conn, "
    SELECT
        stock_issues.*,
        users.name AS issuer
    FROM stock_issues
    LEFT JOIN users ON stock_issues.issued_by = users.id
    WHERE stock_issues.department_id='$department_id'
    AND stock_issues.status='pending'
    ORDER BY stock_issues.id DESC
");

if (!$pending) {
    die("Pending query failed: " . mysqli_error($conn));
}
?>

<div class="md:ml-64 p-6">

    <div class="bg-white rounded-2xl shadow-lg p-6">

        <h1 class="text-3xl font-bold mb-2 text-[#07152B]">
            Pending Department Receipts
        </h1>

        <p class="text-gray-500 mb-6">
            Confirm items issued to your department.
        </p>

        <?php if ($message): ?>
            <div class="bg-green-100 text-green-700 p-3 rounded mb-5">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="overflow-x-auto">

            <table class="w-full">

                <thead>
                    <tr class="bg-slate-100">
                        <th class="p-3 text-left">Issued By</th>
                        <th class="p-3 text-left">Items</th>
                        <th class="p-3 text-left">Note</th>
                        <th class="p-3 text-left">Date</th>
                        <th class="p-3 text-left">Action</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (mysqli_num_rows($pending) > 0): ?>

                        <?php while ($row = mysqli_fetch_assoc($pending)): ?>

                            <?php
                            $issue_id = intval($row['id']);

                            $issue_items = mysqli_query($conn, "
                                SELECT
                                    stock_issue_items.*,
                                    products.name AS product_name,
                                    products.unit
                                FROM stock_issue_items
                                LEFT JOIN products ON stock_issue_items.product_id = products.id
                                WHERE stock_issue_items.stock_issue_id='$issue_id'
                            ");
                            ?>

                            <tr class="border-b">

                                <td class="p-3">
                                    <?php echo htmlspecialchars($row['issuer'] ?? 'N/A'); ?>
                                </td>

                                <td class="p-3">
                                    <?php while ($item = mysqli_fetch_assoc($issue_items)): ?>
                                        <div class="mb-1">
                                            <strong><?php echo htmlspecialchars($item['product_name']); ?></strong>
                                            —
                                            <?php echo (int)$item['quantity']; ?>
                                            <?php echo htmlspecialchars($item['unit'] ?? ''); ?>
                                        </div>
                                    <?php endwhile; ?>
                                </td>

                                <td class="p-3">
                                    <?php echo htmlspecialchars($row['note'] ?? ''); ?>
                                </td>

                                <td class="p-3">
                                    <?php echo date("d M Y h:i A", strtotime($row['created_at'])); ?>
                                </td>

                                <td class="p-3">
                                    <a
                                        href="?receive=<?php echo $row['id']; ?>"
                                        class="bg-green-600 text-white px-4 py-2 rounded-lg"
                                    >
                                        Confirm Received
                                    </a>
                                </td>

                            </tr>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="5" class="p-4 text-center text-gray-500">
                                No pending receipts.
                            </td>
                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<?php include "../includes/footer.php"; ?>