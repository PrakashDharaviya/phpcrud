<?php
include 'config.php';

if (isset($_GET['id'])) {
    $book_id = (int) $_GET['id'];
    if ($book_id <= 0) {
        echo "Book ID not specified.";
        exit();
    }

    $stmt = $conn->prepare("SELECT title, author, dept_id, `Subject`, `Price`, `Publication_Year` FROM books WHERE book_id = ?");
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $title = $row['title'];
        $author = $row['author'];
        $dept_id = $row['dept_id'];
        $subject = $row['Subject'];
        $price = $row['Price'];
        $publication_year = $row['Publication_Year'];
    } else {
        echo "Book not found.";
        exit();
    }
} else {
    echo "Book ID not specified.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_title = trim($_POST['title']);
    $new_author = trim($_POST['author']);
    $new_dept_id = isset($_POST['dept_id']) ? (int) $_POST['dept_id'] : 0;

    $new_subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
    $new_price = isset($_POST['price']) ? (float) $_POST['price'] : 0;
    $new_publication_year = isset($_POST['publication_year']) ? (int) $_POST['publication_year'] : 0;

    $stmt = $conn->prepare("UPDATE books SET title = ?, author = ?, dept_id = ?, `Subject` = ?, `Price` = ?, `Publication_Year` = ? WHERE book_id = ?");
    $stmt->bind_param("ssissii", $new_title, $new_author, $new_dept_id, $new_subject, $new_price, $new_publication_year, $book_id);
    if ($stmt->execute()) {
        header("Location: index.php");
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
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
    <title>Edit Book</title>
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
    <h2>Edit Book</h2>
    <form method="post" action="">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" required>

        <label for="author">Author:</label>
        <input type="text" id="author" name="author" value="<?php echo htmlspecialchars($author); ?>" required>

        <label for="subject">Subject:</label>
        <input type="text" id="subject" name="subject" value="<?php echo htmlspecialchars($subject); ?>" maxlength="100">

        <label for="price">Price:</label>
        <input type="number" id="price" name="price" step="0.01" min="0" value="<?php echo htmlspecialchars($price); ?>">

        <label for="publication_year">Publication Year:</label>
        <input type="number" id="publication_year" name="publication_year" min="0" max="9999" value="<?php echo htmlspecialchars($publication_year); ?>">

        <label for="dept_id">Department:</label>
        <select id="dept_id" name="dept_id" required>
            <?php
            if ($result_departments->num_rows > 0) {
                while($row = $result_departments->fetch_assoc()) {
                    $selected = ($row["dept_Id"] == $dept_id) ? "selected" : "";
                    echo "<option value='".$row["dept_Id"]."' $selected>".$row["dept_name"]."</option>";
                }
            }
            ?>
        </select>

        <input type="submit" value="Update Book">
    </form>
</body>
</html>
