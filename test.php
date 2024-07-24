<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Sator Rwanda</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Saira:wght@500;600;700&display=swap" rel="stylesheet"> 

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom Stylesheet -->
    <style>
        .top-header {
            background-color: #343a40;
            padding: 10px 0;
        }
        .top-header .top-header-content ul {
            margin: 0;
            padding: 0;
            list-style: none;
        }
        .top-header .top-header-content ul li {
            display: inline-block;
            margin-right: 20px;
            color: #ffffff;
        }
        .top-header .top-header-content ul li a {
            color: #ffffff;
            text-decoration: none;
        }
        .header {
            background-color: #007bff;
            padding: 10px 0;
        }
        .logo img {
            max-width: 100%;
        }
        .navigation ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .navigation ul li {
            display: inline-block;
            margin-right: 20px;
        }
        .navigation ul li a {
            color: #ffffff;
            text-decoration: none;
            font-weight: 500;
        }
        .btn-social-icon {
            color: #ffffff;
            margin-right: 5px;
            font-size: 18px;
        }
    </style>
</head>

<body>
    <!-- Top Header -->
    <div class="top-header">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <div class="top-header-content">
                        <ul>
                            <li><i class="fa fa-envelope top-header-icon"></i> info@satorrwanda.rw</li>
                            <li><i class="fa fa-phone top-header-icon"></i> (+250) 728 62 62 68 - (+250) 781 13 81 55</li>
                            <li><i class="fa fa-map-marker top-header-icon"></i> Nyabugogo - Manu plaza - 4th Floor</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4 text-md-right">
                    <div class="top-header-content">
                        <ul>
                            <li>
                                <a href="https://www.facebook.com/satorrwanda" target="_blank" class="btn-social-icon"><i class="fa fa-facebook"></i></a>
                                <a href="https://twitter.com/satorrwanda" target="_blank" class="btn-social-icon"><i class="fa fa-twitter"></i></a>
                                <a href="https://www.instagram.com/satorrwanda/" target="_blank" class="btn-social-icon"><i class="fa fa-instagram"></i></a>
                                <a href="https://www.youtube.com/channel/UCVAEWaCU1xwZvbO6CnkX8Lg" target="_blank" class="btn-social-icon"><i class="fa fa-youtube-play" style="font-size:12px;color:red"></i></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Header Section -->
    <div class="header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                    <div class="logo">
                        <a href="Home"><img src="./images/logo.png" alt="Logo"></a>
                    </div>
                </div>
                <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                    <!-- Navigation -->
                    <div class="navigation">
                        <nav class="navbar navbar-expand-lg navbar-dark">
                            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                                <span class="navbar-toggler-icon"></span>
                            </button>
                            <div class="collapse navbar-collapse" id="navbarNav">
                                <ul class="navbar-nav ml-auto">
                                    <li class="nav-item active">
                                        <a class="nav-link" href="Home">Home</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="About-us">About us</a>
                                    </li>
                                    <li class="nav-item dropdown">
                                        <a class="nav-link dropdown-toggle" href="#" id="servicesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Services
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="servicesDropdown">
                                            <a class="dropdown-item" href="Service-SG">Speed Governors</a>
                                            <a class="dropdown-item" href="Service-GPS">GPS Trackers</a>
                                            <a class="dropdown-item" href="Service-FUEL">Fleet and Fuel MS</a>
                                            <a class="dropdown-item" href="Service-FUEL">X-1R Oil Products</a>
                                        </div>
                                    </li>
                                    <li class="nav-item dropdown">
                                        <a class="nav-link dropdown-toggle" href="#" id="platformDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Platform
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="platformDropdown">
                                            <a class="dropdown-item" href="https://track.satorrwanda.rw:8443" target="_blank">Tracking system</a>
                                            <a class="dropdown-item" href="https://www.tracksolid.com/mainFrame" target="_blank">TrackSolid System</a>
                                            <a class="dropdown-item" href="https://hosting.wialon.eu/?lang=en" target="_blank">Wialon System</a>
                                            <a class="dropdown-item" href="http://197.243.22.95/SatorTech/" target="_blank">SatorTech System</a>
                                        </div>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="Contact-us">Contact us</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="medias.php">Media</a>
                                    </li>
                                </ul>
                            </div>
                        </nav>
                    </div>
                    <!-- /.Navigation -->
                </div>
            </div>
        </div>
    </div>

    <!-- Optional JavaScript -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
