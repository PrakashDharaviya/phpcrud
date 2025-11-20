<?php
include('config.php');
$search = "";
if(isset($_GET['search'])){
    $search = $conn->real_escape_string($_GET['search']);
}
$sql = "SELECT * FROM department";

if($search != ""){
    $sql .= " WHERE dept_name LIKE '%$search%'";
}

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
?>