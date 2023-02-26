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
function getBudgets($con, $user_data)
{
    // Gets all budgets associated to the user_id
    $stmt = $con->prepare("SELECT * FROM budgets WHERE user_id = ?");
    $stmt->bind_param("i", $user_data['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $selected = isset($_POST['budget_id']) && $_POST['budget_id'] == $row['id'] ? 'selected' : '';
        echo "<option value='" . $row['id'] . "' " . $selected . ">" . $row['budget_name'] . "</option>";
    }
}

function calcExpenses($con, $budget_id)
{ 
    // Calculates the total expenses for a budget
    $stmt = $con->prepare("SELECT SUM(expense_amount) AS total FROM expenses WHERE budget_id = ?");
    $stmt->bind_param("i", $budget_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['total'];
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
    <style>
        .menu-bar {
            background-color: #333;
            height: 100vh;
            width: 120px;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            z-index: 1;
        }

        .menu-bar ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .menu-bar li {
            display: block;
            color: white;
            text-align: center;
            padding: 10px;
            text-decoration: none;
            height: 38px;
            margin: 10px;
        }

        .menu-bar li a {
            display: block;
            color: white;
            text-align: center;
            padding: 10px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .menu-bar li a:hover {
            background-color: white;
            color: #333;
            transform: scale(1.1);
        }
    </style>

</head>

<body>
    <div class="menu-bar">
        <ul>
            <li>Budget App</li>
            <li><a href="#">Home</a></li>
            <li><a href="#">Budgets</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>
    <div class="main-content">
        <h2>Hello,
            <?php echo $user_data['name']; ?>
        </h2>
        <div class="container">
            <div class="column">
                <h3>Budget Amount:</h3>
                <p>
                    <?php
                    if (isset($_POST['select_budget']) || !is_null($budget_id) || isset($_POST['delete_expense']) || isset($_POST['add_expense'])) {
                        // Get the budget amount
                        $selected_budget_id = isset($_SESSION['selected_budget_id']) ? $_SESSION['selected_budget_id'] : null;
                        $stmt = $con->prepare("SELECT user_budget FROM budgets WHERE id = ?");
                        $stmt->bind_param("i", $selected_budget_id);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $budget_amount = $result->fetch_assoc()['user_budget'];
                        echo '$' . $budget_amount;
                    }
                    ?>
                </p>
            </div>
            <div class="column">
                <h3>Total Expense:</h3>
                <p>
                    <?php
                    if (!is_null($budget_id) || isset($_POST['delete_expense']) || isset($_POST['add_expense'])) {
                        $selected_budget_id = isset($_SESSION['selected_budget_id']) ? $_SESSION['selected_budget_id'] : null;
                        $total_expense = calcExpenses($con, $selected_budget_id);
                        echo '$' . $total_expense;
                    }
                    ?>
                </p>
            </div>
            <div class="column">
                <h3>Balance:</h3>
                <p>
                    <?php
                    if (!is_null($budget_id) || isset($_POST['delete_expense']) || isset($_POST['add_expense'])) {
                        // Calculate the balance for the budget
                        $selected_budget_id = isset($_SESSION['selected_budget_id']) ? $_SESSION['selected_budget_id'] : null;
                        $total_expense = calcExpenses($con, $selected_budget_id);
                        $stmt = $con->prepare("SELECT user_budget FROM budgets WHERE id = ?");
                        $stmt->bind_param("i", $budget_id);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $budget_amount = $result->fetch_assoc()['user_budget'];
                        $balance = $budget_amount - $total_expense;
                        echo '$' . $balance;
                    }
                    ?>
                </p>
            </div>
        </div>

        <form method="post">
            <input type="text" class="submit-input" name="budget_name" placeholder="Budget name">
            <input type="number" class="submit-input" name="user_budget" placeholder="Budget amount">
            <button type="submit" class="submit-btn" name="create_budget">Create Budget</button>
        </form>

        <form method="post">
            <!-- Budget Selections -->
            <label for="budget_id" class="submit-input">Budget:</label>
            <select class="submit-input" id="budget_id" name="budget_id">
                <?php
                if (isset($_POST['create_budget'])) {
                    if (!empty($budget_name) && $user_budget > 0) {
                        // Create a new budget and save to database under the user_id
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

                    // Unset the budget_id so that it doesn't cause issues when redrawing the page
                    $budget_id = null;
                } else if (isset($_POST['select_budget'])) {
                    $_SESSION['selected_budget_id'] = $_POST['budget_id'];
                    getBudgets($con, $user_data);
                } else {
                    getBudgets($con, $user_data);
                }
                ?>
            </select>
            <button type="submit" class="submit-btn" name="select_budget">Select Budget</button>
            <button type="submit" class="submit-btn" name="delete_budget">Delete Budget</button>
            <?php ?>
            <br>
            <input type="text" class="submit-input" name="expense_name" placeholder="Expense name">
            <input type="number" class="submit-input" name="expense_amount" placeholder="Expense amount">
            <button type="submit" class="submit-btn" name="add_expense">Add Expense</button>
        </form>



        <table>
            <tr>
                <th>Expense</th>
                <th>Amount</th>
                <th>Delete</th>
            </tr>
            <?php
            if (isset($_POST['add_expense'])) {
                $budget_id = $_POST['budget_id'];
                $expense_name = $_POST['expense_name'];
                $expense_amount = $_POST['expense_amount'];

                $stmt = $con->prepare("INSERT INTO expenses (expense_name, expense_amount, budget_id) VALUES (?, ?, ?)");
                $stmt->bind_param("sdi", $expense_name, $expense_amount, $budget_id);
                $stmt->execute();
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
                                <button type='submit' class='delete-expense' name='delete_expense'>Delete Expense</button>
                            </form>
                          </td>";
                    echo "</tr>";
                }
            }

            if (!is_null($budget_id)) {
                $budget_id = $_POST['budget_id'];
                displayExpenses($con, $budget_id);
            }

            if (isset($_POST['delete_expense'])) {
                $selected_budget_id = isset($_SESSION['selected_budget_id']) ? $_SESSION['selected_budget_id'] : null;
                $expense_id = $_POST['expense_id'];
                $stmt = $con->prepare("DELETE FROM expenses WHERE id = ?");
                $stmt->bind_param("i", $expense_id);
                $stmt->execute();

                // Redraw the table of expenses
                displayExpenses($con, $selected_budget_id);
                unset($_POST['delete_expense']);
            }
            ?>
        </table>
    </div>
</body>

</html>