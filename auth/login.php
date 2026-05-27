<?php
session_start();
include "../includes/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query = "
        SELECT users.*, roles.name AS role_name, departments.name AS department_name
        FROM users
        LEFT JOIN roles ON users.role_id = roles.id
        LEFT JOIN departments ON users.department_id = departments.id
        WHERE users.email='$email'
        AND users.status='active'
        LIMIT 1
    ";

    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) == 1) {

        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role_name'];
            $_SESSION['department_id'] = $user['department_id'];
            $_SESSION['department_name'] = $user['department_name'];

            header("Location: ../dashboard/index.php");
            exit();

        } else {
            $error = "Invalid email or password";
        }

    } else {
        $error = "Invalid email or password";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Alim Ops V2</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#07152B] min-h-screen flex items-center justify-center">

    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md">

        <h1 class="text-2xl font-bold text-[#07152B] mb-2">
            Alim Ops V2
        </h1>

        <p class="text-gray-500 mb-6">
            Hotel Inventory & Operations Login
        </p>

        <?php if ($error): ?>
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-5">

            <input
                type="email"
                name="email"
                placeholder="Email Address"
                class="w-full border p-3 rounded-xl"
                required
            >

            <input
                type="password"
                name="password"
                placeholder="Password"
                class="w-full border p-3 rounded-xl"
                required
            >

            <button class="bg-[#07152B] text-white p-3 rounded-xl w-full">
                Login
            </button>

        </form>

    </div>

</body>
</html>