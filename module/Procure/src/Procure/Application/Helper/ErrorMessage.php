<?php
namespace Procure\Application\Helper;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ErrorMessage
{

    public static function showErrorMessage($errors, $dismissible = TRUE)
    {
        $error_msg = "";
        $alert_dismissible = "";
        $close_button = '';

        if ($dismissible) {
            $alert_dismissible = "alert-dismissible";
            $close_button = '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
        }

        if (count($errors) > 0) {
            $error_msg = $error_msg . sprintf('<div class="alert alert-danger %s" role="alert" style="font-size: 9.5pt">[ERROR]:', $alert_dismissible);
            $error_msg = $error_msg . $close_button;
            $error_msg = $error_msg . '<ul>';
            foreach ($errors as $error) :
                $error_msg = $error_msg . '<li>' . $error . '</li>';
            endforeach
            ;
            $error_msg = $error_msg . '</ul>';
            $error_msg = $error_msg . '</div>';
        }
        return $error_msg;
    }
}
