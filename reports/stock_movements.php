<?php
include "../includes/auth.php";
include "../includes/db.php";
include "../includes/header.php";
include "../includes/sidebar.php";

$role = $_SESSION['role'] ?? '';
$department_id = $_SESSION['department_id'] ?? null;

if ($role === 'department_user') {

    $issued = mysqli_query($conn, "
        SELECT
            stock_issues.created_at,
            stock_issues.status,
            stock_issues.note,
            stock_issue_items.quantity,
            products.name AS product_name,
            departments.name AS department_name,
            users.name AS staff_name,
            'Issued To Department' AS movement_type
        FROM stock_issues
        LEFT JOIN stock_issue_items ON stock_issues.id = stock_issue_items.stock_issue_id
        LEFT JOIN products ON stock_issue_items.product_id = products.id
        LEFT JOIN departments ON stock_issues.department_id = departments.id
        LEFT JOIN users ON stock_issues.issued_by = users.id
        WHERE stock_issues.department_id = '$department_id'
        ORDER BY stock_issues.id DESC
    ");

    $usage = mysqli_query($conn, "
        SELECT
            stock_usage.created_at,
            'used' AS status,
            stock_usage.note,
            stock_usage.quantity,
            products.name AS product_name,
            departments.name AS department_name,
            users.name AS staff_name,
            'Department Usage' AS movement_type
        FROM stock_usage
        LEFT JOIN products ON stock_usage.product_id = products.id
        LEFT JOIN departments ON stock_usage.department_id = departments.id
        LEFT JOIN users ON stock_usage.used_by = users.id
        WHERE stock_usage.department_id = '$department_id'
        ORDER BY stock_usage.id DESC
    ");

} else {

    $stock_in = mysqli_query($conn, "
        SELECT
            activity_logs.created_at,
            'completed' AS status,
            activity_logs.details AS note,
            '-' AS quantity,
            '-' AS product_name,
            'General Store' AS department_name,
            users.name AS staff_name,
            'Stock In / Product Activity' AS movement_type
        FROM activity_logs
        LEFT JOIN users ON activity_logs.user_id = users.id
        WHERE activity_logs.action IN ('Stock In', 'Added Product')
        ORDER BY activity_logs.id DESC
    ");

    $issued = mysqli_query($conn, "
        SELECT
            stock_issues.created_at,
            stock_issues.status,
            stock_issues.note,
            stock_issue_items.quantity,
            products.name AS product_name,
            departments.name AS department_name,
            users.name AS staff_name,
            'Issued To Department' AS movement_type
        FROM stock_issues
        LEFT JOIN stock_issue_items ON stock_issues.id = stock_issue_items.stock_issue_id
        LEFT JOIN products ON stock_issue_items.product_id = products.id
        LEFT JOIN departments ON stock_issues.department_id = departments.id
        LEFT JOIN users ON stock_issues.issued_by = users.id
        ORDER BY stock_issues.id DESC
    ");

    $usage = mysqli_query($conn, "
        SELECT
            stock_usage.created_at,
            'used' AS status,
            stock_usage.note,
            stock_usage.quantity,
            products.name AS product_name,
            departments.name AS department_name,
            users.name AS staff_name,
            'Department Usage' AS movement_type
        FROM stock_usage
        LEFT JOIN products ON stock_usage.product_id = products.id
        LEFT JOIN departments ON stock_usage.department_id = departments.id
        LEFT JOIN users ON stock_usage.used_by = users.id
        ORDER BY stock_usage.id DESC
    ");
}
?>

<div class="md:ml-64 p-6">

    <div class="bg-white rounded-2xl shadow-lg p-6">

        <h1 class="text-3xl font-bold text-[#07152B] mb-2">
            Stock Movement Report
        </h1>

        <p class="text-gray-500 mb-6">
            Track stock flow from General Store to departments and usage.
        </p>

        <div class="overflow-x-auto">

            <table class="w-full">

                <thead>
                    <tr class="bg-slate-100">
                        <th class="p-3 text-left">Date</th>
                        <th class="p-3 text-left">Movement</th>
                        <th class="p-3 text-left">Department</th>
                        <th class="p-3 text-left">Product</th>
                        <th class="p-3 text-left">Qty</th>
                        <th class="p-3 text-left">Status</th>
                        <th class="p-3 text-left">Staff</th>
                        <th class="p-3 text-left">Note</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if ($role !== 'department_user' && isset($stock_in)): ?>
                        <?php while ($row = mysqli_fetch_assoc($stock_in)): ?>
                            <tr class="border-b">
                                <td class="p-3"><?php echo date("d M Y h:i A", strtotime($row['created_at'])); ?></td>
                                <td class="p-3 font-semibold"><?php echo $row['movement_type']; ?></td>
                                <td class="p-3"><?php echo $row['department_name']; ?></td>
                                <td class="p-3"><?php echo $row['product_name']; ?></td>
                                <td class="p-3"><?php echo $row['quantity']; ?></td>
                                <td class="p-3"><?php echo ucfirst($row['status']); ?></td>
                                <td class="p-3"><?php echo $row['staff_name'] ?? 'N/A'; ?></td>
                                <td class="p-3"><?php echo htmlspecialchars($row['note']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>

                    <?php while ($row = mysqli_fetch_assoc($issued)): ?>
                        <tr class="border-b">
                            <td class="p-3"><?php echo date("d M Y h:i A", strtotime($row['created_at'])); ?></td>
                            <td class="p-3 font-semibold"><?php echo $row['movement_type']; ?></td>
                            <td class="p-3"><?php echo htmlspecialchars($row['department_name']); ?></td>
                            <td class="p-3"><?php echo htmlspecialchars($row['product_name']); ?></td>
                            <td class="p-3"><?php echo (int)$row['quantity']; ?></td>
                            <td class="p-3"><?php echo ucfirst($row['status']); ?></td>
                            <td class="p-3"><?php echo htmlspecialchars($row['staff_name'] ?? 'N/A'); ?></td>
                            <td class="p-3"><?php echo htmlspecialchars($row['note'] ?? ''); ?></td>
                        </tr>
                    <?php endwhile; ?>

                    <?php while ($row = mysqli_fetch_assoc($usage)): ?>
                        <tr class="border-b">
                            <td class="p-3"><?php echo date("d M Y h:i A", strtotime($row['created_at'])); ?></td>
                            <td class="p-3 font-semibold"><?php echo $row['movement_type']; ?></td>
                            <td class="p-3"><?php echo htmlspecialchars($row['department_name']); ?></td>
                            <td class="p-3"><?php echo htmlspecialchars($row['product_name']); ?></td>
                            <td class="p-3"><?php echo (int)$row['quantity']; ?></td>
                            <td class="p-3"><?php echo ucfirst($row['status']); ?></td>
                            <td class="p-3"><?php echo htmlspecialchars($row['staff_name'] ?? 'N/A'); ?></td>
                            <td class="p-3"><?php echo htmlspecialchars($row['note'] ?? ''); ?></td>
                        </tr>
                    <?php endwhile; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<?php include "../includes/footer.php"; ?>