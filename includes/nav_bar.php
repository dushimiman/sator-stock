<!DOCTYPE html>
<html>
<head>
    <title>Admin Home</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .navbar {
            background-color: #f8f9fa;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1); 
        }
        .navbar-brand img {
            max-height: 50px; 
            margin-right: 10px; 
        }
        .navbar-nav {
            margin-left: auto; 
        }
        .nav-item.dropdown:hover .dropdown-menu {
            display: block;
            margin-top: 0; 
        }
       
        .dropdown-menu {
            margin-top: 0;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light">
        <a class="navbar-brand" href="#">
            <img src="./images/logo.png" alt="Logo"> 
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="admin_home.php">Home</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="itemsDropdown1" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Items
                    </a>
                    <div class="dropdown-menu" aria-labelledby="itemsDropdown1">
                        <a class="dropdown-item" href="add_item.php">Add Item</a>
                        <a class="dropdown-item" href="view_all_item.php">All Item</a>
                        <a class="dropdown-item" href="edited_items.php">View Edited Item</a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="itemsDropdown2" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Request Item
                    </a>
                    <div class="dropdown-menu" aria-labelledby="itemsDropdown2">
                        <a class="dropdown-item" href="request_form.php">New Request</a>
                        <a class="dropdown-item" href="view_requisitions.php">Requests List</a>
                        <a class="dropdown-item" href="returned_form.php">Return item</a>
                        <a class="dropdown-item" href="item_returned.php">View item returned</a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="itemsDropdown3" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                       Report
                    </a>
                    <div class="dropdown-menu" aria-labelledby="itemsDropdown3">
                        <a class="dropdown-item" href="index.php">Stock Report</a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
