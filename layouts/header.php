<?php $user = current_user(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>
        <?php if (!empty($page_title)) echo remove_junk($page_title);
              elseif (!empty($user)) echo ucfirst($user['name']);
              else echo "Inventory Management System";?>
    </title>
    <link rel="icon" href="libs/images/icon.png" type="image/x-icon"/>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="libs/css/fonts-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="libs/css/main.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<?php if ($session->isUserLoggedIn()): ?>
    <header id="header">
        <div class="logo pull-left">ANC MINI MART</div>
        <div class="header-content">
            <div class="header-date pull-left">
                <strong>
                    <?php
                    date_default_timezone_set('Asia/Manila'); 
                    echo date("F j, Y, g:i a");
                    ?>
                </strong>
            </div>
            <div class="pull-right clearfix">
                <ul class="info-menu list-inline list-unstyled">
                    <li class="profile">
                        <a href="#" data-toggle="dropdown" class="toggle" aria-expanded="false">
                            <img src="uploads/users/<?php echo $user['image'];?>" alt="user-image" class="img-circle img-inline">
                            <span><?php echo remove_junk(ucfirst($user['name'])); ?> <i class="caret"></i></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="profile.php?id=<?php echo (int)$user['id'];?>">
                                    <i class="glyphicon glyphicon-user"></i>
                                    Profile
                                </a>
                            </li>
                            <li>
                                <a href="edit_account.php" title="edit account">
                                    <i class="glyphicon glyphicon-cog"></i>
                                    Settings
                                </a>
                            </li>
                            <li class="last">
                                <a href="logout.php">
                                    <i class="glyphicon glyphicon-off"></i>
                                    Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </header>
    <div class="sidebar">
        <?php if($user['user_level'] === '1'): ?>
            <!-- admin menu -->
            <?php include_once('admin_menu.php');?>

        <?php elseif($user['user_level'] === '2'): ?>
            <!-- Special user -->
            <?php include_once('special_menu.php');?>

        <?php elseif($user['user_level'] === '3'): ?>
            <!-- User menu -->
            <?php include_once('user_menu.php');?>

        <?php endif; ?>
    </div>
<?php endif; ?>

<?php if ($msg): ?>
<script>
    Swal.fire({
        icon: '<?php echo $msg['type']; ?>',
        title: '<?php echo $msg['message']; ?>',
        position: 'center',
        showConfirmButton: true
    });
</script>
<?php endif; ?>

<div class="page">
    <div class="container-fluid">
        <!-- Page content goes here -->
    </div>
</div>
</body>
</html>
