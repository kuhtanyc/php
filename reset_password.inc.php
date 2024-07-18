<?php
    require 'dbh.inc.php';

    if (isset($_POST["reset-password-submit"])) {
        
        $selector = $_POST["selector"];
        $validator = $_POST["validator"];
        $password = $_POST["pwd"];
        $passwordRepeat = $_POST["pwd-repeat"];
        
        if (empty($password) || empty($passwordRepeat)) {
            header("Location: /create_new_password?error=password_missing");
            exit();
        }
        else if ($password != $passwordRepeat) {
            header("Location: /create_new_password?error=password_mismatch");
            exit();
        }
        
        $currentDate = date("U");
        
        $sql = "SELECT * FROM pwdReset WHERE pwdResetSelector=? AND pwdResetExpires >= $currentDate";
        $stmt = mysqli_stmt_init($conn);
        if(!mysqli_stmt_prepare($stmt, $sql)) {
    	    die("sql statement error");
        } else {
    	    mysqli_stmt_bind_param($stmt,"s",$selector);
    	    mysqli_stmt_execute($stmt);
        }
        $result = mysqli_stmt_get_result($stmt);
        
        if (!$row = mysqli_fetch_assoc($result)) {
            echo "You need to re-submit your reset request.";
            exit();
        }
        else {
            $tokenBin = hex2bin($validator);
            $tokenCheck = password_verify($tokenBin,$row["pwdResetToken"]);
            
            if ($tokenCheck == false) {
                echo "You need to re-submit your reset request.";
                exit();
            } elseif ($tokenCheck == true) {
                $tokenEmail = $row['pwdResetEmail'];

                $sql = "SELECT * FROM users WHERE userEmail=?;";
                $stmt = mysqli_stmt_init($conn);
                if(!mysqli_stmt_prepare($stmt, $sql)) {
    	            die("sql statement error");
                } else {
                    mysqli_stmt_bind_param($stmt,"s",$tokenEmail);
    	              mysqli_stmt_execute($stmt);
    	              $result = mysqli_stmt_get_result($stmt);
                    if (!$row = mysqli_fetch_assoc($result)) {
                        echo "There was an error!";
                        exit();
                    }
                    else {
                        $sql = "UPDATE users SET userPwd=? WHERE userEmail=?;";
                        $stmt = mysqli_stmt_init($conn);
                        if(!mysqli_stmt_prepare($stmt, $sql)) {
    	                    die("sql statement error");
                        } else {
                            $newPwdHash = password_hash($password, PASSWORD_DEFAULT);
                            mysqli_stmt_bind_param($stmt,"ss",$newPwdHash,$tokenEmail);
    	                      mysqli_stmt_execute($stmt);
    	                    
    	                      $sql = "DELETE FROM pwdReset WHERE pwdResetEmail=?;";
                            $stmt = mysqli_stmt_init($conn);
                            if(!mysqli_stmt_prepare($stmt, $sql)) {
    	                        die("sql statement error");
                            } else {
        	                    mysqli_stmt_bind_param($stmt,"s", $userEmail);
    	                        mysqli_stmt_execute($stmt);
    	                        header("Location: /password_reset?newpwd=passwordupdated");
                            }
                        }
                    }
                }
            }
        }
    }
    else {
        header("Location: /");
    }
?>
