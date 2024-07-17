<?php
session_start();
include 'dbh.inc.php';

$username = $_SESSION['username'];

if (isset($_POST['commentSubmit'])) {
    
    $userName = $_POST['userName'];
    $date = $_POST['date'];
    $comment = $_POST['comment'];
    $comItem = $_POST['comItem'];
    $comLink = $_POST['comLink'];
    
    $comment = addslashes($comment);
    
    $url = 'https://www.artistgfx.com';
    
    $beat=@date(B);
    
    if (empty($comment)) {
        header("Location: ".$url.$comItem."?comment=empty");
        exit(); 
    } else {
        $sql = "INSERT INTO comments (comDate,comment,comItem,userId,beat) VALUES ('$date', '$comment', '$comItem','$userName','$beat')";
        $result = mysqli_query($conn, $sql);
        
        $sqlpoints = "UPDATE users SET userPoints=userPoints + 1 WHERE userName='$userName'";
        $result = mysqli_query($conn, $sqlpoints);
        
        //commentEmail($userName, $comment, $comItem);
    
        header("Location: ".$url.$comItem."#".$comLink."_comments");
        //https://www.artistgfx.com/members/deandude/images/bobafett#bobafett_comments
        exit();  
    }
}

function getCommentsCount($conn, $comItem) {
    $sql = "SELECT comment FROM comments 
            WHERE comItem = '$comItem';";
    $result = mysqli_query($conn, $sql);
    $row_count = mysqli_num_rows($result);
    echo "$row_count";
    //$sql = "SELECT count(*) from comments where comItem='$comItem';"; 
    //$result = mysqli_query($conn,$sql);
    //$count = mysqli_fetch_array($result);
    //echo "<font color=\"#000000\" face=\"arial\" size=\"1\">".$count[0]."</font>";
}

function getComments($conn, $comItem) {
    
    $username = $_SESSION['username'];
    
    $sql = "SELECT * FROM comments 
            WHERE comItem = '$comItem'
            ORDER BY comDate DESC;";
    $result = mysqli_query($conn, $sql);
    $row_count = mysqli_num_rows($result);
    
    echo "<table width=100% border=0 cellpadding=1 cellspacing=4>"; 
    
    while ($row = mysqli_fetch_assoc($result)) {
        
        echo "<tr><td valign='top' width=30% bgcolor=#969dbc>";
        echo "
                <table border=0 cellpadding=1 cellspacing=0 width=100% bgcolor=#8b91a9>
                    <tr>
                        <td width=20% valign='top' bgcolor=#969dbc>
                            <table border=0 width=100% cellpadding=1 cellspacing=0>
                                <tr>
                                    <td colspan=3>
        ";
                                        
        $sqlUser = "SELECT users.userName, users.userAvatar, userAvatars.avatarID, userAvatars.avatarName, userAvatars.avatarDesc FROM users, userAvatars WHERE userName='$row[userID]' and users.userAvatar=userAvatars.avatarID";
        $resultUser = mysqli_query($conn, $sqlUser);
                                        
        while ($rowUser = mysqli_fetch_assoc($resultUser)) 
        {
            echo "<img src='/avatars/".$rowUser[avatarName].".png' width=16 height=16 title='".$rowUser[avatarDesc]."'>&nbsp;";
            echo "<font face=arial size=1 color=#000000><b><a href='/members/".$rowUser[userName]."'>".$rowUser['userName']."</a></b></font>";
            echo "            </td>";
            echo "        </tr>";
            echo "        <tr>";
            echo "            <td width=50%>";

                                $sqlTier ="SELECT userTier FROM users where userName='$rowUser[userName]'"; 
                                $resultTier = mysqli_query($conn,$sqlTier);
                                while($rowTier = mysqli_fetch_assoc($resultTier)) {
                                    if ($rowTier['userTier'] == 1){echo "<img src='/images/tier_one.png' height=15 width=8 title='Tier 1'>";}   
                                    elseif ($rowTier['userTier'] == 2){echo "<img src='/images/tier_one.png' height=15 width=8 title='Tier 1'><img src='/images/tier_two.png' height=15 width=8 title='Tier 2'>";}   
                                    elseif ($rowTier['userTier'] == 3){echo "<img src='/images/tier_one.png' height=15 width=8 title='Tier 1'><img src='/images/tier_two.png' height=15 width=8 title='Tier 2'><img src='/images/tier_three.png' height=15 width=8 title='Tier 3'>";}     
                                    elseif ($rowTier['userTier'] == 4){echo "<img src='/images/tier_one.png' height=15 width=8 title='Tier 1'><img src='/images/tier_two.png' height=15 width=8 title='Tier 2'><img src='/images/tier_three.png' height=15 width=8 title='Tier 3'><img src='/images/tier_four.png' height=15 width=8 title='Tier 4'>";} 
                                    elseif ($rowTier['userTier'] == 5){echo "<img src='/images/tier_one.png' height=15 width=8 title='Tier 1'><img src='/images/tier_two.png' height=15 width=8 title='Tier 2'><img src='/images/tier_three.png' height=15 width=8 title='Tier 3'><img src='/images/tier_four.png' height=15 width=8 title='Tier 4'><img src='/images/tier_five.png' height=15 width=8 title='Tier 5'>";} 
                                    elseif ($rowTier['userTier'] == 6){echo "<img src='/images/tier_one.png' height=15 width=8 title='Tier 1'><img src='/images/tier_two.png' height=15 width=8 title='Tier 2'><img src='/images/tier_three.png' height=15 width=8 title='Tier 3'><img src='/images/tier_four.png' height=15 width=8 title='Tier 4'><img src='/images/tier_five.png' height=15 width=8 title='Tier 5'><img src='/images/tier_six.png' height=15 width=8 title='Tier 6'>";}    
                                    elseif ($rowTier['userTier'] == 7){echo "<img src='/images/tier_one.png' height=15 width=8 title='Tier 1'><img src='/images/tier_two.png' height=15 width=8 title='Tier 2'><img src='/images/tier_three.png' height=15 width=8 title='Tier 3'><img src='/images/tier_four.png' height=15 width=8 title='Tier 4'><img src='/images/tier_five.png' height=15 width=8 title='Tier 5'><img src='/images/tier_six.png' height=15 width=8 title='Tier 6'><img src='/images/tier_seven.png' height=15 width=8 title='Tier 7'>";} 
                                }
                                
            echo "            <img src='/images/accessory_82.png' height=16 width=16 title='First 1000 Members'>&nbsp;</td>";
            echo "        </tr>";
            echo "<tr>";
            echo "<td colspan=3>";
        
                            
        $date = $row['comDate'];
        $date = date("m/d/Y", strtotime($date));
    
        echo "<font face='arial' size='1' color='#000000'>".$date."&nbsp;@".$row['beat']."</font>";
        echo "</td>";
        echo "</tr>";
        echo "</table>";
        echo "</td>";
        echo "<td width=8% valign='top' bgcolor=#969dbc><center>";
        echo "<img src='https://artistgfx.com/members/".$rowUser['userName']."/images/".$rowUser['userName']."_avatar.png' width=50 width=50>";
        }
        
        echo "</td>";
        echo "<td width=72% valign='top' bgcolor=#a2a8c3>";
        echo "<table border=0 cellpadding=2 cellspacing=2 width=100% style=\"table-layout: fixed; width: 100%\">";
        echo "<tr><td valign='top' width=95% style=\"word-wrap: break-word\"><font face=arial size=1 color=#000000>".nl2br($row['comment'])."</td>";
        echo "<td width=5% valign='top'><div class='comment-edit'>";
        echo "</div></td></tr></table>";
        echo "</td>";
        echo "</tr>";
        echo "</table>";
        echo "</div>";
    }
    echo "</td></tr></table>";
    echo "</div>";
}

function commentEmail($username, $comment, $comPage) {
    $to = 'deandude@artistgfx.com';
    $subject = 'A new ArtistGFX comment was created!';
    $message = ''.$username.' created the following comment in '.$comPage. "\n\n" .
               '"'.$comment.'"';
    $headers = 'From: deandude@artistgfx.com' . "\r\n" .
        'Reply-To: deandude@artistgfx.com' . "\r\n" .
        'X-Mailer: PHP/' . phpversion();
    mail($to, $subject, $message, $headers);
}
