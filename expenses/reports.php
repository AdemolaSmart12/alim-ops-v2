<?php
include "../includes/auth.php";
include "../includes/db.php";
include "../includes/header.php";
include "../includes/sidebar.php";

$expenses = mysqli_query($conn, "
    SELECT
        expenses.*,
        expense_categories.name AS category_name,
        users.name AS added_by_name
    FROM expenses
    LEFT JOIN expense_categories ON expenses.category_id = expense_categories.id
    LEFT JOIN users ON expenses.added_by = users.id
    ORDER BY expenses.expense_date DESC, expenses.id DESC
");

$total_expenses = 0;
?>

<div class="md:ml-64 p-6">

    <div class="bg-white rounded-2xl shadow-lg p-6">

        <h1 class="text-3xl font-bold text-[#07152B] mb-2">
            Expense Report
        </h1>

        <p class="text-gray-500 mb-6">
            Track all hotel operational expenses.
        </p>

        <div class="overflow-x-auto">

            <table class="w-full">

                <thead>
                    <tr class="bg-slate-100">
                        <th class="p-3 text-left">Date</th>
                        <th class="p-3 text-left">Title</th>
                        <th class="p-3 text-left">Category</th>
                        <th class="p-3 text-left">Amount</th>
                        <th class="p-3 text-left">Added By</th>
                        <th class="p-3 text-left">Note</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if ($expenses && mysqli_num_rows($expenses) > 0): ?>

                        <?php while ($row = mysqli_fetch_assoc($expenses)): ?>
                            <?php $total_expenses += $row['amount']; ?>

                            <tr class="border-b">
                                <td class="p-3">
                                    <?php echo date("d M Y", strtotime($row['expense_date'])); ?>
                                </td>

                                <td class="p-3 font-semibold">
                                    <?php echo htmlspecialchars($row['title']); ?>
                                </td>

                                <td class="p-3">
                                    <?php echo htmlspecialchars($row['category_name'] ?? 'N/A'); ?>
                                </td>

                                <td class="p-3 font-bold text-red-600">
                                    ₦<?php echo number_format($row['amount'], 2); ?>
                                </td>

                                <td class="p-3">
                                    <?php echo htmlspecialchars($row['added_by_name'] ?? 'N/A'); ?>
                                </td>

                                <td class="p-3">
                                    <?php echo htmlspecialchars($row['note'] ?? ''); ?>
                                </td>
                            </tr>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="6" class="p-4 text-center text-gray-500">
                                No expenses recorded yet.
                            </td>
                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

        <div class="mt-6 bg-red-50 text-red-700 p-5 rounded-xl">
            <p class="text-sm">Total Expenses</p>
            <h2 class="text-3xl font-bold">
                ₦<?php echo number_format($total_expenses, 2); ?>
            </h2>
        </div>

    </div>

</div>

<?php include "../includes/footer.php"; ?>