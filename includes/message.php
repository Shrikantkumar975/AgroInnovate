<?php
// includes/message.php

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $message_type = $_SESSION['message_type'] ?? 'info'; // Default to 'info' if type is not set

    // Determine the appropriate Bootstrap alert class
    $alert_class = '';
    switch ($message_type) {
        case 'success':
            $alert_class = 'alert-success';
            break;
        case 'danger':
            $alert_class = 'alert-danger';
            break;
        case 'warning':
            $alert_class = 'alert-warning';
            break;
        case 'info':
        default:
            $alert_class = 'alert-info';
            break;
    }
    ?>
    <div class="alert <?php echo $alert_class; ?> alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($message); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php
    // Clear the session messages so they don't display again on the next page load
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}
?>