<?php
include('config.php');

// incoming parameters
$search = "";
if(isset($_GET['search'])){
    $search = $conn->real_escape_string($_GET['search']);
}

$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$per_page = isset($_GET['per_page']) ? max(1, (int)$_GET['per_page']) : 10;

$where = '';
if($search != ""){
    $where = " WHERE books.title LIKE '%$search%' OR books.author LIKE '%$search%' OR books.Subject LIKE '%$search%' OR department.dept_name LIKE '%$search%'";
}

// total count for pagination
$countSql = "SELECT COUNT(*) AS cnt FROM books LEFT JOIN department ON books.dept_id = department.dept_Id" . $where;
$countRes = $conn->query($countSql);
$total = 0;
if ($countRes) {
    $totRow = $countRes->fetch_assoc();
    $total = (int)$totRow['cnt'];
}

$total_pages = max(1, (int) ceil($total / $per_page));

$offset = ($page - 1) * $per_page;

$sql = "SELECT books.book_id, books.title, books.author, books.Subject, books.Price, books.Publication_Year, department.dept_name FROM books LEFT JOIN department ON books.dept_id = department.dept_Id" . $where . " LIMIT $offset, $per_page";

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

// render pagination controls (simple)
$pagination = '<div class="pagination" style="text-align:center;margin:10px 0;">';
if ($total > 0) {
    // previous
    if ($page > 1) {
        $pagination .= "<a href=\"#\" data-page='".($page-1)."' class='page-link' style=\"margin:0 6px;\">&laquo; Prev</a>";
    } else {
        $pagination .= "<span style=\"margin:0 6px;color:#999;\">&laquo; Prev</span>";
    }

    // page numbers (show up to 7 pages centered)
    $start = max(1, $page - 3);
    $end = min($total_pages, $page + 3);
    for ($p = $start; $p <= $end; $p++) {
        if ($p == $page) {
            $pagination .= "<strong style='margin:0 4px;'>$p</strong>";
        } else {
            $pagination .= "<a href=\"#\" data-page='$p' class='page-link' style=\"margin:0 4px;\">$p</a>";
        }
    }

    // next
    if ($page < $total_pages) {
        $pagination .= "<a href=\"#\" data-page='".($page+1)."' class='page-link' style=\"margin:0 6px;\">Next &raquo;</a>";
    } else {
        $pagination .= "<span style=\"margin:0 6px;color:#999;\">Next &raquo;</span>";
    }
}
$pagination .= "</div>";

echo "<!--PAGINATION-->" . $pagination;

?>
