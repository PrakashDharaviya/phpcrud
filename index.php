<?php
include 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System</title>
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
        table{
            margin: 20px auto;
            border-collapse: collapse;
            width: 80%;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        a{
            text-decoration: none;
            color: #007bff;
        }
        h2,h3{
            margin-bottom: 20px;
        }

    </style>
</head>
<body>
    <h2>Library Management System</h2>
    <h3>Department</h3>
    <div style="display:inline-block; margin-bottom:10px;">
        <input type="text" id="deptSearch" placeholder="Search Departments...">
        <select id="deptPerPage">
            <option value="5">5 / page</option>
            <option value="10">10 / page</option>
            <option value="25">25 / page</option>
        </select>
    </div>
    <br><br>
    
    <a href="add_dept.php">Add Department</a>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Action</th>
        </tr>
        <tbody id="deptTable">
        <?php
        $sql = "SELECT * FROM department";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>".$row["dept_Id"]."</td>
                        <td>".$row["dept_name"]."</td>
                        <td>
                            <a href='edit_dept.php?id=".$row["dept_Id"]."'>Edit</a> |
                            <a href='delete_dept.php?id=".$row["dept_Id"]."'>Delete</a>
                        </td>
                    </tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No departments found</td></tr>";
        }
        ?>
    </table>
    <!-- Department pagination  -->
    <div id="deptPagination"></div>
    <br><br>
    <h3>Books</h3>
    <div style="display:inline-block; margin-bottom:10px;">
        <input type="text" id="bookSearch" placeholder="Search Books...">
        <select id="bookPerPage">
            <option value="5">5 / page</option>
            <option value="10" selected>10 / page</option>
            <option value="25">25 / page</option>
        </select>
    </div>
    <br><br>
    <a href="add_book.php">Add Book</a>
    
    <table>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Author</th>
            <th>Department</th>
            <th>Subject</th>
            <th>Price</th>
            <th>Publication Year</th>
            <th>Action</th>
        </tr>
        <tbody id="bookTable">
        <?php
        // show all books
        $sql = "SELECT books.book_id, books.title, books.author, books.Subject, books.Price, books.Publication_Year, department.dept_name FROM books LEFT JOIN department ON books.dept_id = department.dept_Id";
        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>". (int)$row["book_id"] ."</td>
                        <td>".htmlspecialchars($row["title"]) ."</td>
                        <td>".htmlspecialchars($row["author"]) ."</td>
                        <td>".htmlspecialchars($row["dept_name"]) ."</td>
                        <td>".htmlspecialchars($row["Subject"]) ."</td>
                        <td>".htmlspecialchars($row["Price"]) ."</td>
                        <td>".htmlspecialchars($row["Publication_Year"]) ."</td>
                        <td>
                            <a href='edit_book.php?id=".(int)$row["book_id"]."'>Edit</a> |
                            <a href='delete_book.php?id=".(int)$row["book_id"]."'>Delete</a>
                        </td>
                    </tr>";
            }
        } else {
            echo "<tr><td colspan='8'>No books found</td></tr>";
        }
        ?>
        </tbody>
    </table>
    <div id="bookPagination"></div>

    <script>
        function debounce(fn, wait) {
            let t;
            return function(...args) {
                clearTimeout(t);
                t = setTimeout(() => fn.apply(this, args), wait);
            };
        }

        function fetchRows(url, targetTableId, targetPaginationId) {
            const req = new XMLHttpRequest();
            req.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    const parts = this.responseText.split('<!--PAGINATION-->');
                    document.getElementById(targetTableId).innerHTML = parts[0] || '';
                    document.getElementById(targetPaginationId).innerHTML = parts[1] || '';
                }
            };
            req.open('GET', url, true);
            req.send();
        }

        // department search
        function searchDept(page = 1) {
            const s = document.getElementById('deptSearch').value || '';
            const per = document.getElementById('deptPerPage').value || 5;
            const url = `search_dept.php?search=${encodeURIComponent(s)}&page=${page}&per_page=${per}&t=${Date.now()}`;
            fetchRows(url, 'deptTable', 'deptPagination');

            setTimeout(() => {
                document.querySelectorAll('.dept-page-link').forEach(el => {
                    el.addEventListener('click', function(e){
                        e.preventDefault();
                        const p = parseInt(this.getAttribute('data-page')) || 1;
                        searchDept(p);
                    });
                });
            }, 100);
        }

        // book search
        function searchBook(page = 1) {
            const s = document.getElementById('bookSearch').value || '';
            const per = document.getElementById('bookPerPage').value || 10;
            const url = `search_book.php?search=${encodeURIComponent(s)}&page=${page}&per_page=${per}&t=${Date.now()}`;
            fetchRows(url, 'bookTable', 'bookPagination');
            setTimeout(() => {
                document.querySelectorAll('.page-link').forEach(el => {
                    el.addEventListener('click', function(e){
                        e.preventDefault();
                        const p = parseInt(this.getAttribute('data-page')) || 1;
                        searchBook(p);
                    });
                });
            }, 100);
        }

        const debouncedDeptSearch = debounce(() => searchDept(1), 300);
        const debouncedBookSearch = debounce(() => searchBook(1), 300);

        document.getElementById('deptSearch').addEventListener('keyup', debouncedDeptSearch);
        document.getElementById('deptPerPage').addEventListener('change', () => searchDept(1));

        document.getElementById('bookSearch').addEventListener('keyup', debouncedBookSearch);
        document.getElementById('bookPerPage').addEventListener('change', () => searchBook(1));

        // initial paginated load
        document.addEventListener('DOMContentLoaded', function() {
            searchDept(1);
            searchBook(1);
        });
    </script>
</body>
</html>