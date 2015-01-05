<?php
class EmailSender
{
    public static function sendMail($to)//send mail with unique-link to user and returns unique part of unique-link
    {
        $num = 44;
        $unique = '';

        $arr= array('a','b','c','d','e','f',
            'g','h','i','j','k','l',
            'm','n','o','p','r','s',
            't','u','v','x','y','z',
            'A','B','C','D','E','F',
            'G','H','I','J','K','L',
            'M','N','O','P','R','S',
            'T','U','V','X','Y','Z',
            '1','2','3','4','5','6',
            '7','8','9','0','.',',',
            '(',')','[',']','!','?',
            '&','^','%','@','*','$',
            '<','>','/','|','+','-',
            '{','}','`','~');

        for($i = 1; $i <= $num; $i++){
            $index = mt_rand(0, count($arr) - 1);
            $unique .= $arr[$index];
        }

        $uniqueQuery = ROOT_PATH . "/user/confirm?link=" . urlencode($unique);

        $link ="<a href = $uniqueQuery>" . $uniqueQuery . "</a>";

        $to = (filter_var($to,FILTER_VALIDATE_EMAIL)) ? $to : false;

        $subject = 'Registration';

        $message = "Congratulations you were successfully registered on adsboard2.zone, please follow next link $link to complete your registration";

        $headers = 'From: webmaster@example.com' . "\r\n" . 'Reply-To: webmaster@example.com' . "\r\n" . 'X-Mailer: PHP/' . phpversion();
         return $unique;//returns unique part of unique-link what were sent to user, should be write in DB to table user to use in ConfirmAction() in future

        if(mail($to, $subject, $message, $headers)){
            echo 'Email were sent';
        }else{
            echo 'Please try again later..';
        }
    }
}