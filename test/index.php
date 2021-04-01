<?php
    // header('location: ../index.php');

    if (file_exists("../../models/ev/")) {
        echo "existe";
    }else {
        echo "No existe";
    }