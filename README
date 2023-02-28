# Basic Budget App using PHP and MySQL

Fully manual budget app to track your expenses. Created using PHP and MYSQL with XAMPP to control local server.

![budget-app](https://user-images.githubusercontent.com/36823677/221728913-d36a1804-29fd-46f7-8c6d-86f5a503b68c.png)

Working:
- Backend full functional
- Calculates the budget, expenses, and balance correctly

To Do:
- Add a dashboard showing general information

Installation Instructions:
1. Download XAMPP
2. Navigate to where XAMPP is installed (typically C:\xampp) and launch the xammp-control.exe
3. Navigate to the htdoc folder in the xampp folder
4. Download as ZIP and extract to folder or git clone to folder
5. Create the MySQL tables in phpMyAdmin
```
CREATE TABLE users (
  user_id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  password VARCHAR(255) NOT NULL
);

CREATE TABLE budgets (
  id INT PRIMARY KEY AUTO_INCREMENT,
  budget_name VARCHAR(255) NOT NULL,
  user_budget DECIMAL(10, 2) NOT NULL,
  user_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE expenses (
  id INT PRIMARY KEY AUTO_INCREMENT,
  expense_name VARCHAR(255) NOT NULL,
  expense_amount DECIMAL(10, 2) NOT NULL,
  budget_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (budget_id) REFERENCES budgets(id)
);
```
6. The website can be viewed under http://localhost/Budget-app-php/index.php#
