<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $dept_id = isset($_POST['dept_id']) ? (int) $_POST['dept_id'] : 0;
    $subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
    $price = isset($_POST['price']) ? (float) $_POST['price'] : 0;
    $publication_year = isset($_POST['publication_year']) ? (int) $_POST['publication_year'] : 0;

    if ($dept_id <= 0) {
        echo "Invalid department selected.";
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO books (title, author, dept_id, `Subject`, `Price`, `Publication_Year`) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssisdi", $title, $author, $dept_id, $subject, $price, $publication_year);
    if ($stmt->execute()) {
        header("Location: index.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
        exit();
    }
}

$sql_departments = "SELECT * FROM department";
$result_departments = $conn->query($sql_departments);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Book</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            text-align: center;
        }
        h2 {
            color: #333;
        }
        form {
            margin: 20px auto;
            width: 50%;
            text-align: left;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"], input[type="number"], select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h2>Add Book</h2>
    <form method="post" action="">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required>

        <label for="author">Author:</label>
        <input type="text" id="author" name="author" required>

        <label for="subject">Subject:</label>
        <input type="text" id="subject" name="subject" maxlength="100">

        <label for="price">Price:</label>
        <input type="number" id="price" name="price" step="0.01" min="0">

        <label for="publication_year">Publication Year:</label>
        <input type="number" id="publication_year" name="publication_year" min="0" max="9999">

        <label for="dept_id">Department:</label>
        <select id="dept_id" name="dept_id" required>
            <?php
            if ($result_departments->num_rows > 0) {
                while($row = $result_departments->fetch_assoc()) {
                    echo "<option value='".$row["dept_Id"]."'>".$row["dept_name"]."</option>";
                }
            }
            ?>
        </select>

        <input type="submit" value="Add Book">
    </form>
</body>
</html>
