<?php
declare(strict_types=1);
?>
<!doctype html> <!-- Important: must specify -->
<html>
<head>
    <meta charset="utf-8"> <!-- Important: rapi-doc uses utf8 characters -->
    <script type="module" src="https://unpkg.com/rapidoc/dist/rapidoc-min.js"></script>
    <title>API Documentation</title>
</head>
<body>
<rapi-doc
    spec-url="/api/docs"
    render-style="focused"
    use-path-in-nav-bar="true"
    allow-spec-file-load = "false"
    show-method-in-nav-bar = "as-colored-block"
    nav-active-item-marker = "colored-block"
    schema-style="table"
    nav-item-spacing="compact"

>
</rapi-doc>
</body>
</html>
