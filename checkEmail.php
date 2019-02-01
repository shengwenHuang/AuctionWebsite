<?php
function validateEmail($fieldName) {
    if checkEmpty($fieldName, False) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "";
          }
        else {
            $emailErr = "Please enter a valid email"
        }
    }
    else {
        $emailErr = "Invalid email format";
    }
    return $emailErr
}
?>