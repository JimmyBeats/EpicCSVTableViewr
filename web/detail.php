<?php

// Bootstrap it
require_once('../bootstrap.php');

// Call the Controller
$controller = new \Jimmy\EpicCSVTableViewr\Controller\UserDataController($app, $_GET);

// Get the user record
$user = $controller->getUserDetail();

// 404 if not found
if (!$user) {
    http_response_code(404);
    exit;
}

// Calculate the age from the DOB
$from = new DateTime($user['dob']);
$to   = new DateTime('today');
$age = $from->diff($to)->y;

# Return the snippet for the modal. In real life I would have used a template engine like Twig

?>
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title"><?php echo htmlentities($user['first_name'] . " " . $user['last_name']) ?></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <table class="table-condensed">
                <tr>
                    <td>
                        ID
                    </td>
                    <td>
                        <?php echo htmlentities($user['user_id']) ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        Name
                    </td>
                    <td>
                        <?php echo htmlentities($user['first_name'] . " " . $user['last_name']) ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        Email
                    </td>
                    <td>
                        <?php echo htmlentities($user['email']) ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        Role
                    </td>
                    <td>
                        <?php echo htmlentities($user['role']) ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        Age
                    </td>
                    <td>
                        <?php echo $age ?>
                    </td>
                </tr>
            </table>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>
