<?php
if (isset($_POST['signup-submit'])) {

    require 'Dbconnection.php'; //connection to DB
    include 'Auto_generate.php';

        //GET DATA FROM REGISTER
        $email = $_POST['email'];
        $username = $_POST['nama'];
        $icnum = $_POST['ic'];
        $numP = $_POST['numphone'];
        $jaw = $_POST['JawJab'];
        $status = $_POST['status'];

        //AUTO GENERATE
        $id = random_num(5);
        $pass = random_num(6);

        //FUNCTION INVALID INPUT
        function validate_inputs($input) {
            return preg_match("/^[a-zA-Z0-9]*$/", $input);//i changed the quantifier so the string must contain a character as well
        }

        //ERROR HANDLER

            //check empty fill
            if (empty($email) || empty($username) || empty($icnum) || empty($numP) || empty($numP) || empty($jaw)) {
                header("Location: ../registerJabatan.php?error=emptyfields&email=$email&nama=$username&ic=$icnum&numphone=$numP&JawJab=$jaw");
                exit();
            }
            //check invalid username and email
            else if (!filter_var($email, FILTER_VALIDATE_EMAIL) && validate_inputs($username) && validate_inputs($jaw)){
                header("Location: ../registerJabatan.php?error=invalidEmail,Nama,Jawatan&ic=$icnum&numphone=$numP");
            }
            //Check invaild email
            else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                header("Location: ../registerJabatan.php?error=invaildMail&nama=$username&ic=$icnum&numphone=$numP&JawJab=$jaw");
                exit();
            }
            //Check invalid username and Jawatan
            else if(validate_inputs($username) && validate_inputs($jaw)){
                header("Location: ../registerJabatan.php?error=invalidUsername,Jawatan&mail=$email&ic=$icnum&numphone=$numP");
                exit();
            }
            //TAMBAHAN - IC or NomPhone
            else{
                //Check if USername has been taken 
                $sql = "SELECT namaPegawai FROM registerjabatan WHERE namaPegawai=?;";

                //PREPARE STATEMENT
                $stmt = mysqli_stmt_init($conn); //connection DB
                    //if code query fail 
                    if(!mysqli_stmt_prepare($stmt, $sql)){
                        header("Location ../registerJabatan.php?error=sqlerror");
                        exit();
                    } 
                    else{
                        //CHECKING DATA SECTION
                        mysqli_stmt_bind_param($stmt, "s", $username);

                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_store_result($stmt);
                        $resultsCheck = mysqli_stmt_num_rows($stmt); //set as Array 
                        //if same variable output be 1>0 mean username has been taken 
                        if($resultsCheck > 0){
                            header("Location: ../registerJabatan.php?error=Usernametaken&email=$email&ic=$icnum&numphone=$numP&JawJab=$jaw");
                            exit();
                        }
                        else{
                            //INSERT INTO DATA TO DB

                            $sql = "INSERT INTO registerjabatan (emailUsers,namaPegawai,noIC,nomFon,jawatan,keadaan, idPegawai, passPegawai) VALUES (?, ?, ?, ?, ?, ?, ?, ?);";

                            //Prepare statement
                            $stmt = mysqli_stmt_init($conn); //connection
                            //if sql query code fail 
                            if(!mysqli_stmt_prepare($stmt, $sql)){
                                header("Location: ../registerJabatan.php?error=sqlerror");
                                exit();
                            }
                            else{
                                mysqli_stmt_bind_param($stmt, "ssiissis", $email, $username, $icnum, $numP, $jaw, $status, $id, $pass);
                                mysqli_stmt_execute($stmt);
                                header("Location: ../registerJabatan.php?register=success");
                                exit();
                            }

                        }
                        //sending email to majikan and operation email account
                        $to = $email;
                        $subject = "Pengesahan Pendaftaran";

                        // $message = 'Dear '.$_POST['name'].',<br>';
                        $message = 'Salam Sejahtera '.$username.', <br>';
                        $message .= "Akaun anda telah berjaya didaftarkan. Terima kasih kerana menyertai kami.<br><br>";
                        $message .= "Sila log masuk ke akaun anda untuk memuat naik dokumen yang disenaraikan di dalam sistem untuk semakan<br>";
                        $message .= "dan mendapatkan kelulusan dari pihak H.O.M.E SYSTEM sebelum berhubung dengan jabatan<br>";
                        $message .= "atau agensi untuk penyerahan pelatih.<br>";
                        $message .= "<br>";
                        $message .= "Berikut merupakan maklumat yang telah anda daftarkan sebentar tadi:<br>";
                        $message .= "<br>";
                        $message .= "Akaun baru anda <br>";
                        $message .= "Username:  '$username'<br>";
                        $message .= "Id:  '$id'<br>";
                        $message .= "Kata Laluan: '$pass'<br>";
                        $message .= "<br><br>";
                        $message .= "<a href='https://homesystem.my/'>Log masuk ke akuan anda</a><br>";
                        $message .= "<br><br>";
                        $message .= "<br><br>";
                        $message .= "boleh hubungi kami di  03-87519598 atau<br>";
                        $message .= "email kami di contact@homesystem.my<br>";

                        // Always set content-type when sending HTML email
                        $headers = "MIME-Version: 1.0" . "\r\n";
                        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

                        // More headers
                        $headers .= 'From: <account@homesystem.my>' . "\r\n";
                        $headers .= 'Cc: account@homesystem.my' . "\r\n";
                        $headers .= 'Cc: unitpermohonan@homesystem.my' . "\r\n";

                        mail($to,$subject,$message,$headers);
                        
                    }

            }
}
else{
    //without clicking signup button
    header("Location: ../registerJabatan.php?error=emptyfields&email=$email&name=$username&ic=$icnum&numphone=$numP&JawJab=$jaw");
    exit();
}
