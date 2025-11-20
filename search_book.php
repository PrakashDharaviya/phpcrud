<?php
include('config.php');

$search = "";
if(isset($_GET['search'])){
    $search = $conn->real_escape_string($_GET['search']);
}

$sql = "SELECT books.book_id, books.title, books.author, books.Subject, books.Price, books.Publication_Year, department.dept_name FROM books LEFT JOIN department ON books.dept_id = department.dept_Id";

if($search != ""){
    $sql .= " WHERE books.title LIKE '%$search%' OR books.author LIKE '%$search%' OR books.Subject LIKE '%$search%' OR department.dept_name LIKE '%$search%'";
}

$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $book_id = (int)$row['book_id'];
        $title = htmlspecialchars($row['title']);
        $author = htmlspecialchars($row['author']);
        $dept = htmlspecialchars($row['dept_name']);
        $subject = htmlspecialchars($row['Subject']);
        $price = htmlspecialchars($row['Price']);
        $pubyear = htmlspecialchars($row['Publication_Year']);

        echo "<tr>
                <td>{$book_id}</td>
                <td>{$title}</td>
                <td>{$author}</td>
                <td>{$dept}</td>
                <td>{$subject}</td>
                <td>{$price}</td>
                <td>{$pubyear}</td>
                <td>
                    <a href='edit_book.php?id={$book_id}'>Edit</a> |
                    <a href='delete_book.php?id={$book_id}'>Delete</a>
                </td>
            </tr>";
    }
} else {
    echo "<tr><td colspan='8'>No books found</td></tr>";
}

?>
