<?php
session_start();
include("connection.php");
include("functions.php");

$user_data = check_login($con);
$budget_id = isset($_POST['budget_id']) ? $_POST['budget_id'] : null;

if (!is_null($budget_id)) {
    $stmt = $con->prepare("SELECT * FROM expenses WHERE budget_id = ?");
    $stmt->bind_param("i", $budget_id);
    $stmt->execute();
    $result = $stmt->get_result();
}
function displayExpenses($con, $budget_id)
{
    $stmt = $con->prepare("SELECT * FROM expenses WHERE budget_id = ?");
    $stmt->bind_param("i", $budget_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Output the expenses in the table
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['expense_name'] . "</td>";
        echo "<td>$" . $row['expense_amount'] . "</td>";
        echo "<td>
                    <form method='post'>
                        <input type='hidden' name='expense_id' value='" . $row['id'] . "'>
                        <button type='submit' name='delete_expense'>Delete Expense</button>
                    </form>
                  </td>";
        echo "</tr>";
    }
}

if (isset($_POST['add_expense'])) {
    $budget_id = $_POST['budget_id'];
    $expense_name = $_POST['expense_name'];
    $expense_amount = $_POST['expense_amount'];

    $stmt = $con->prepare("INSERT INTO expenses (expense_name, expense_amount, budget_id) VALUES (?, ?, ?)");
    $stmt->bind_param("sdi", $expense_name, $expense_amount, $budget_id);
    $stmt->execute();
    getBudgets($con, $user_data);
}

if (isset($_POST['delete_expense'])) {

    $expense_id = $_POST['expense_id'];
    $stmt = $con->prepare("DELETE FROM expenses WHERE id = ?");
    $stmt->bind_param("i", $expense_id);
    $stmt->execute();

    $budget_id = isset($_POST['budget_id']) ? $_POST['budget_id'] : null;
    displayExpenses($con, $budget_id);
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Budget App</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Raleway:wght@300;400;500;600&family=Roboto:wght@400;500;700&display=swap"
        rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
</head>

<body>
    <h1>BUDGET APP</h1>
    <h2>Hello,
        <?php echo $user_data['name']; ?>
    </h2>
    <a href="logout.php">Logout</a>

    <form method="post">
        <input type="text" name="budget_name" placeholder="Budget name">
        <input type="number" name="user_budget" placeholder="Budget amount">
        <button type="submit" name="create_budget">Create Budget</button>
    </form>

    <form method="post">
        <!-- Budget Selections -->
        <label for="budget_id">Budget:</label>
        <select id="budget_id" name="budget_id">
            <?php
            function getBudgets($con, $user_data)
            {
                $stmt = $con->prepare("SELECT * FROM budgets WHERE user_id = ?");
                $stmt->bind_param("i", $user_data['user_id']);
                $stmt->execute();
                $result = $stmt->get_result();

                while ($row = $result->fetch_assoc()) {
                    $selected = isset($_POST['budget_id']) && $_POST['budget_id'] == $row['id'] ? 'selected' : '';
                    echo "<option value='" . $row['id'] . "' " . $selected . ">" . $row['budget_name'] . "</option>";
                }
            }

            if (isset($_POST['create_budget'])) {
                if (!empty($budget_name) && $user_budget > 0) {
                    $budget_name = $_POST['budget_name'];
                    $user_budget = $_POST['user_budget'];
                    $stmt = $con->prepare("INSERT INTO budgets (user_id, budget_name, user_budget) VALUES (?, ?, ?)");
                    $stmt->bind_param("isd", $user_data['user_id'], $budget_name, $user_budget);
                    $stmt->execute();

                    // Get the ID of the newly created budget
                    $budget_id = $stmt->insert_id;

                    getBudgets($con, $user_data);
                } else {
                    echo "Please enter a budget name and amount";
                }
            } else if (isset($_POST['delete_budget'])) {
                // Delete all expenses attached to the budget_id
                $budget_id = $_POST['budget_id'];
                $stmt = $con->prepare("DELETE FROM expenses WHERE budget_id = ?");
                $stmt->bind_param("i", $budget_id);
                $stmt->execute();

                // Delete the Budget
                $stmt = $con->prepare("DELETE FROM budgets WHERE id = ?");
                $stmt->bind_param("i", $budget_id);
                $stmt->execute();
                getBudgets($con, $user_data);
            } else {
                getBudgets($con, $user_data);
            }
            ?>
        </select>
        <button type="submit" name="select_budget">Select Budget</button>
        <button type="submit" name="delete_budget">Delete Budget</button>
        <?php

        ?>
        <br>
        <input type="text" name="expense_name" placeholder="Expense name">
        <input type="number" name="expense_amount" placeholder="Expense amount">
        <button type="submit" name="add_expense">Add Expense</button>
    </form>

    <table>
        <tr>
            <th>Name</th>
            <th>Amount</th>
            <th>Delete</th>
        </tr>
        <?php
        if (!is_null($budget_id)) {
            $budget_id = $_POST['budget_id'];
            displayExpenses($con, $budget_id);
        }
        
// function deleteExpense($con, $budget_id) {
//     $expense_id = $_POST['expense_id'];
//     $stmt = $con->prepare("DELETE FROM expenses WHERE id = ?");
//     $stmt->bind_param("i", $expense_id);
//     $stmt->execute();

//     // $budget_id = isset($_POST['budget_id']) ? $_POST['budget_id'] : null;
//     displayExpenses($con, $budget_id);

// }

// if (isset($_POST['delete_expense'])) {
//     deleteExpense($con, $budget_id);
// }
        ?>
    </table>

</body>

</html>
