<?php
include('config.php');
// parameters
$search = "";
if(isset($_GET['search'])){
    $search = $conn->real_escape_string($_GET['search']);
}

$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$per_page = isset($_GET['per_page']) ? max(1, (int)$_GET['per_page']) : 5;

$where = '';
if($search != ""){
    $where = " WHERE dept_name LIKE '%$search%'";
}

$countSql = "SELECT COUNT(*) AS cnt FROM department" . $where;
$countRes = $conn->query($countSql);
$total = 0;
if ($countRes) {
    $r = $countRes->fetch_assoc();
    $total = (int)$r['cnt'];
}

$total_pages = max(1, (int) ceil($total / $per_page));
$offset = ($page - 1) * $per_page;

$sql = "SELECT * FROM department" . $where . " LIMIT $offset, $per_page";

$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['dept_Id']}</td>
                <td>{$row['dept_name']}</td>
                <td>
                    <a href='edit_dept.php?id={$row['dept_Id']}'>Edit</a> |
                    <a href='delete_dept.php?id={$row['dept_Id']}'>Delete</a>
                </td>
            </tr>";
    }
} else {
    echo "<tr><td colspan='3'>No departments found</td></tr>";
}

// pagination
$pagination = '<div class="pagination" style="text-align:center;margin:10px 0;">';
//Prev button
if ($total > 0) {
    if ($page > 1) {
        $pagination .= "<a href=\"#\" data-page='".($page-1)."' class='dept-page-link' style=\"margin:0 6px;\">&laquo; Prev</a>";
    } else {
        $pagination .= "<span style=\"margin:0 6px;color:#999;\">&laquo; Prev</span>";
    }

    $start = max(1, $page - 3);
    $end = min($total_pages, $page + 3);
    for ($p = $start; $p <= $end; $p++) {
        if ($p == $page) {
            $pagination .= "<strong style='margin:0 4px;'>$p</strong>";
        } else {
            $pagination .= "<a href=\"#\" data-page='$p' class='dept-page-link' style=\"margin:0 4px;\">$p</a>";
        }
    }
    //Next button
    if ($page < $total_pages) {
        $pagination .= "<a href=\"#\" data-page='".($page+1)."' class='dept-page-link' style=\"margin:0 6px;\">Next &raquo;</a>";
    } else {
        $pagination .= "<span style=\"margin:0 6px;color:#999;\">Next &raquo;</span>";
    }
}
$pagination .= "</div>";

echo "<!--PAGINATION-->" . $pagination;
?>