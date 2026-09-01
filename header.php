<nav>
    <a href="<?php echo BASE; ?>/index.php">Travel Guide</a>
    <a href="<?php echo BASE; ?>/index.php">Home</a>
    <a href="<?php echo BASE; ?>/places.php">Places</a>

    <?php if (loggedIn()): ?>

        <a href="<?php echo BASE; ?>/dashboard.php">Dashboard</a>
        <a href="<?php echo BASE; ?>/profile.php">Profile</a>

        <?php if (isUser()): ?>
            <a href="<?php echo BASE; ?>/wishlist.php">Wishlist</a>
            <a href="<?php echo BASE; ?>/trip.php">Trips</a>
        <?php endif; ?>

        <?php if (isScout()): ?>
            <a href="<?php echo BASE; ?>/scout/create.php">Submit Place</a>
            <a href="<?php echo BASE; ?>/scout/requests.php">My Requests</a>
            <a href="<?php echo BASE; ?>/scout/approved.php">Approved</a>
        <?php endif; ?>

        <?php if (isAdmin()): ?>
            <a href="<?php echo BASE; ?>/admin/dashboard.php">Admin</a>
        <?php endif; ?>

        <a href="<?php echo BASE; ?>/logout.php">Logout</a>

    <?php else: ?>

        <a href="<?php echo BASE; ?>/login.php">Login</a>
        <a href="<?php echo BASE; ?>/register.php">Register</a>

    <?php endif; ?>
</nav>