<?php
require_once 'page.php';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>分页展示</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<nav class="pagination-nav">
    <?php $page = new Pagination(100, 10);
    echo $page->outPagination(); ?>
</nav>
</body>
</html>