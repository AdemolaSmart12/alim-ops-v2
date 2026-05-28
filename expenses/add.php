<?php
include "../includes/auth.php";
include "../includes/db.php";
include "../includes/header.php";
include "../includes/sidebar.php";

$message = "";

$categories = mysqli_query($conn, "
    SELECT * FROM expense_categories
    ORDER BY name ASC
");

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $category_id = intval($_POST['category_id']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $amount = floatval($_POST['amount']);
    $expense_date = $_POST['expense_date'];
    $note = mysqli_real_escape_string($conn, $_POST['note']);
    $added_by = $_SESSION['user_id'] ?? null;

    $insert = mysqli_query($conn, "
        INSERT INTO expenses (
            category_id,
            added_by,
            title,
            amount,
            expense_date,
            note
        ) VALUES (
            '$category_id',
            '$added_by',
            '$title',
            '$amount',
            '$expense_date',
            '$note'
        )
    ");

    if ($insert) {
        $message = "Expense added successfully.";
    } else {
        $message = "Error adding expense: " . mysqli_error($conn);
    }
}
?>

<div class="md:ml-64 p-6">

    <div class="bg-white p-8 rounded-2xl shadow-lg max-w-3xl">

        <h1 class="text-2xl font-bold mb-6 text-[#07152B]">
            Add Expense
        </h1>

        <?php if ($message): ?>
            <div class="bg-green-100 text-green-700 p-3 rounded mb-5">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-5">

            <select name="category_id" class="w-full border p-3 rounded-xl" required>
                <option value="">Select Expense Category</option>

                <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
                    <option value="<?php echo $cat['id']; ?>">
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <input
                name="title"
                placeholder="Expense Title"
                class="w-full border p-3 rounded-xl"
                required
            >

            <input
                type="number"
                step="0.01"
                name="amount"
                placeholder="Amount"
                class="w-full border p-3 rounded-xl"
                required
            >

            <input
                type="date"
                name="expense_date"
                class="w-full border p-3 rounded-xl"
                required
            >

            <textarea
                name="note"
                placeholder="Note"
                class="w-full border p-3 rounded-xl"
            ></textarea>

            <button class="bg-[#07152B] text-white px-6 py-3 rounded-xl w-full">
                Save Expense
            </button>

        </form>

    </div>

</div>

<?php include "../includes/footer.php"; ?>