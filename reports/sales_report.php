<?php
include "../includes/auth.php";
include "../includes/db.php";
include "../includes/header.php";
include "../includes/sidebar.php";

$role = $_SESSION['role'] ?? '';
$department_id = $_SESSION['department_id'] ?? null;

if ($role === 'department_user') {
    $sales = mysqli_query($conn, "
        SELECT
            department_sales.*,
            departments.name AS department_name,
            products.name AS product_name,
            users.name AS sold_by_name
        FROM department_sales
        LEFT JOIN departments ON department_sales.department_id = departments.id
        LEFT JOIN products ON department_sales.product_id = products.id
        LEFT JOIN users ON department_sales.sold_by = users.id
        WHERE department_sales.department_id='$department_id'
        ORDER BY department_sales.id DESC
    ");
} else {
    $sales = mysqli_query($conn, "
        SELECT
            department_sales.*,
            departments.name AS department_name,
            products.name AS product_name,
            users.name AS sold_by_name
        FROM department_sales
        LEFT JOIN departments ON department_sales.department_id = departments.id
        LEFT JOIN products ON department_sales.product_id = products.id
        LEFT JOIN users ON department_sales.sold_by = users.id
        ORDER BY department_sales.id DESC
    ");
}

$total_sales = 0;
?>

<div class="md:ml-64 p-6">

    <div class="bg-white rounded-2xl shadow-lg p-6">

        <h1 class="text-3xl font-bold text-[#07152B] mb-2">
            Sales Report
        </h1>

        <p class="text-gray-500 mb-6">
            Track sales recorded by departments.
        </p>

        <div class="overflow-x-auto">

            <table class="w-full">

                <thead>
                    <tr class="bg-slate-100">
                        <th class="p-3 text-left">Date</th>
                        <th class="p-3 text-left">Department</th>
                        <th class="p-3 text-left">Product</th>
                        <th class="p-3 text-left">Quantity</th>
                        <th class="p-3 text-left">Unit Price</th>
                        <th class="p-3 text-left">Total</th>
                        <th class="p-3 text-left">Sold By</th>
                        <th class="p-3 text-left">Note</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if ($sales && mysqli_num_rows($sales) > 0): ?>

                        <?php while ($row = mysqli_fetch_assoc($sales)): ?>
                            <?php $total_sales += $row['total_amount']; ?>

                            <tr class="border-b">
                                <td class="p-3">
                                    <?php echo date("d M Y h:i A", strtotime($row['created_at'])); ?>
                                </td>

                                <td class="p-3">
                                    <?php echo htmlspecialchars($row['department_name']); ?>
                                </td>

                                <td class="p-3 font-semibold">
                                    <?php echo htmlspecialchars($row['product_name']); ?>
                                </td>

                                <td class="p-3">
                                    <?php echo (int)$row['quantity']; ?>
                                </td>

                                <td class="p-3">
                                    ₦<?php echo number_format($row['unit_price'], 2); ?>
                                </td>

                                <td class="p-3 font-bold text-green-600">
                                    ₦<?php echo number_format($row['total_amount'], 2); ?>
                                </td>

                                <td class="p-3">
                                    <?php echo htmlspecialchars($row['sold_by_name'] ?? 'N/A'); ?>
                                </td>

                                <td class="p-3">
                                    <?php echo htmlspecialchars($row['note'] ?? ''); ?>
                                </td>
                            </tr>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="8" class="p-4 text-center text-gray-500">
                                No sales recorded yet.
                            </td>
                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

        <div class="mt-6 bg-green-50 text-green-700 p-5 rounded-xl">
            <p class="text-sm">Total Sales</p>
            <h2 class="text-3xl font-bold">
                ₦<?php echo number_format($total_sales, 2); ?>
            </h2>
        </div>

    </div>

</div>

<?php include "../includes/footer.php"; ?>