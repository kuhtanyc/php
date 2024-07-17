<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

session_start();
include 'dbh.inc.php';
include 'member_profile.inc.php';

function sayWhat($conn,$saywhat,$username) {
    $beat=@date(B);
    
    $sql = "INSERT INTO saywhat (saywhat,userName,beat,created_on) VALUES ('$saywhat','$username','$beat',NOW());";
    $result = mysqli_query($conn,$sql);
    
    $sqlPoints = "UPDATE users SET userPoints=userPoints+1 WHERE userName='$username'";
    mysqli_query($conn, $sqlPoints);

    header("location: /");
    //exit();
}

function userTier($memberName,$conn) {
    
    $sql ="SELECT userTier FROM users where userName = '$memberName';"; 
    $result = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($result)) {
        $tier = $row['userTier'];
    }
    if ($tier=="1"){$tier="tier_one";}
    if ($tier=="2"){$tier="tier_two";}    
    if ($tier=="3"){$tier="tier_three";}    
    if ($tier=="4"){$tier="tier_four";}
    if ($tier=="5"){$tier="tier_five";}
    if ($tier=="6"){$tier="tier_six";}    
    if ($tier=="7"){$tier="tier_seven";}   
    
    if($tier=="tier_one"){$tier = "<img src='/images/$tier.png' height=15 width=8 title='Tier 1'>";}
    
    return $tier;  
}

function createUser($conn,$memberName,$password,$emailAddress,$gender,$birthDateMonth,$birthDateDay,$birthDateYear,$country,$exposeEmail,$exposeBirthDate,$keepUpdated) {
    
    if ($gender=='M') {
        $userAvatar="5";
    } else {
        $userAvatar="4";
    }
    
    //04-28-1975
    $myDate = $birthDateMonth.'-'.$birthDateDay.'-'.$birthDateYear;
    
    $bday = DateTime::createFromFormat('m-d-Y', $myDate)->format('Y-m-d');

    $sql = "INSERT INTO users (created_on,userAvatar,userEmail,userName,userPoints,userPwd,userStatus,userGender,userBday,userCountry,exposeEmail,exposeBday,keepUpdated,userBadges,userLoggedIn,userTier) VALUES (NOW(),'$userAvatar','$emailAddress','$memberName',10,'$password','M','$gender','$bday','$country','$exposeEmail','$exposeBirthDate','$keepUpdated',1,'N',1);";
    $result = mysqli_query($conn,$sql);

    header("location: /signup_success?registration=success");
    createMemberDirs($conn,$memberName,$emailAddress,$gender,$bday,$country);
    signupEmail($emailAddress,$memberName);
    exit();
}

function createMemberDirs($conn,$memberName,$emailAddress,$gender,$bday,$country) {
    $dir = '../../members/'.$memberName.'';

    //this was difficult to figure out!
    if (!file_exists($dir)) {
        mkdir('../../members/'.$memberName.'', 0755, true);
        mkdir('../../members/'.$memberName.'/images', 0755, true);
        touch('../../members/'.$memberName.'/index.php');
        touch('../../members/'.$memberName.'/images/'.$memberName.'_banner.png', 0755, true);
        
        $chmod = "0777";
        chmod('../../members/'.$memberName.'/index.php', octdec($chmod));
        chmod('../../members/'.$memberName.'/images', octdec($chmod));
        createProfilePage($conn,$memberName,$emailAddress,$gender,$bday,$country);

        $bannerName = $memberName . "_banner.png";
        $avatarName = $memberName . "_avatar.png";
        $member_banner = '../../images/default_profile_banner.gif';
        $member_avatar = '../../images/avatar.png';
	    $bannerDestination = "../../members/" . $memberName . "/images/$bannerName";
	    $avatarDestination = "../../members/" . $memberName . "/images/$avatarName";	    
	    chmod("../../members/" . $memberName . "/images/$bannerName", octdec($chmod));
	    chmod("../../members/" . $memberName . "/images/$avatarName", octdec($chmod));
	    
        copy($member_banner, $bannerDestination);
        copy($member_avatar, $avatarDestination);
    }
    else {
        header("location: /signup?error=directory_exists");
    }
}

function signupEmail($emailAddress, $memberName) {
    //Load Composer's autoloader
    require 'vendor/autoload.php';

    //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer();

    $to = $emailAddress;
    $subject = 'Registration Success - Welcome to the ArtistGFX community '.$memberName.'!';
    $message = 'Thank you for creating a member account at ArtistGFX.com!<p>Your new member name is: '.$memberName.'<p>You now have the ability to make comments, chat, and use the many other features of the site. Please use the member panel at the top of the site to login and configure your account.<p>If you have any technical issues or general feedback, please send an email to support@artistgfx.com. Thanks again!<p>- ArtistGFX Team';
    
    try {
        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;                            //Enable verbose debug output
        $mail->isSMTP();                                                  //Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                             //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                         //Enable SMTP authentication
        $mail->Username   = 'artistgfx24@gmail.com';                      //SMTP username
        $mail->Password   = '';                                           //SMTP password (gmail app secret)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;                  //Enable implicit TLS encryption
        $mail->Port       = 465;                                          //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom('artistgfx24@gmail.com', 'ArtistGFX Support');
        $mail->addAddress($to, '');                                       //Add a recipient
        $mail->addReplyTo('support@artistgfx.com', 'ArtistGFX Support');

        //Content
        $mail->isHTML(true);                                              //Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $message;
        $mail->AltBody = $message;

        $mail->send();
        echo 'Message has been sent';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

?>
