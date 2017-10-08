<!DOCTYPE html>
<?php

function check_name($fname)
{
	$arash = array('jpg','JPG','JPEG','bmp','BMP','gif','GIF','png','PNG');
	$rpos = strrpos($fname,'.');
	$rash = substr($fname, ($rpos+1));

	foreach ($arash as $r)
	{
		if ($rash == $r)
		{
			return true;
		}
	}
	return false;
}

	if (count($_POST)>0){
		$name = trim($_POST['name']);
		$mail = trim($_POST['mail']);
		$phone = trim($_POST['phone']);
		$comment = trim($_POST['comment']);
        $dt = date("Y-m-d H:i:s");
		
		if (strlen($name)<2){
			$msg = 'Занадто короткий імя';
		}
		elseif(strlen($mail)<2){
			$msg = 'Занадто короткий E-mail';
		}
		elseif(!is_numeric($phone)){
			$msg = 'Телефон введений невірно';
		}
        elseif(strlen($comment)<1){
			$msg = 'Введіть опис проблеми';
		}
        elseif($_FILES["filename1"]["size"] > 1024*10*1024){
            $msg = 'Розмір файлу перевищує 10 мб';
        }
        elseif(!is_uploaded_file($_FILES["filename1"]["tmp_name"])){
            // Проверяем загружен ли файл, если нет, то выводим сообщение
            $msg = 'Помилка завантаження файлу';
        }
    	elseif(!check_name($_FILES["filename1"]["name"])){
            // Проверяем загружен ли файл, если нет, то выводим сообщение
            $msg = 'Неправильне розширення файлу';
        }
		else{
            $dt = htmlspecialchars($dt);
            $name = htmlspecialchars($name);
            $mail = htmlspecialchars($mail);
            $phone = htmlspecialchars($phone);
            $comment = htmlspecialchars($comment);
            
            //Сохраняем введенные данные в файл
			file_put_contents('apps.txt', "$dt-!-$name-!-$mail-!-$phone-!-$comment\r\n", FILE_APPEND);
            
            
            //Даннные для отправки письма
            $to_name = 'Заявка Eho Pro';//Отправитель
            $to = "alienwarem11x3@gmail.com";//Ваш E-mail




            $header = "From: <EhoPro@example.com>\r\n";
            $header .= "Reply-To: EhoPro@example.com\r\n";

            $subject = "Заявка Eho Pro";//Тема письма

            $textmsg ="Надійшла заявка<br>
                        Дата: $dt<br>
                        $name<br>
                        E-mail: $mail<br>
                        Телефон: $phone<br>
                        Коментар: $comment";

            $un        = strtoupper(uniqid(time()));

            if ( !empty( $_FILES['filename1']['tmp_name'] ) and $_FILES['filename1']['error'] == 0 ) {

                $filepath = $_FILES['filename1']['tmp_name'];
                $filename = $_FILES['filename1']['name'];

            $f=fopen($filepath,"r");




            $header     .= "X-Mailer: PHPMail Tool\n";
            $header     .= "Mime-Version: 1.0\n";
            $header     .= "Content-Type:multipart/mixed;";
            $header     .= "boundary=\"----------".$un."\"\n\n";

            $message       = "------------".$un."\nContent-Type:text/html;\n";
            $message      .= "Content-Transfer-Encoding: 8bit\n\n$textmsg\n\n";
            $message      .= "------------".$un."\n";
            $message      .= "Content-Type: application/octet-stream;";
            $message      .= "name=\"".basename($filename)."\"\n";
            $message      .= "Content-Transfer-Encoding:base64\n";
            $message      .= "Content-Disposition:attachment;";
            $message      .= "filename=\"".basename($filename)."\"\n\n";
            $message      .= chunk_split(base64_encode(fread($f,filesize($filepath))))."\n";



              }


            if (mail ($to,$subject,$message, $header))
            {
              //
            } else {
             //print "Не могу отправить письмо !!!";   
            }

			$name = '';
            $mail = '';
            $phone = '';
            $comment = '';
            
			$msg = 'Ваша заявка прийнята, чекайте дзвінка!';
		}
	}
	else{
		$name = '';
		$mail = '';
		$phone = '';
		$comment = '';
		
		$msg = 'Заповніть поля та натисніть кнопку "Відправити"';
	}
?>

<html>
    <head>
        <meta charset="utf-8">
		<title>EhoPro-консультація</title>
		<link rel="stylesheet" type="text/css" href="style/order.css">
    </head>
    <body>
        <div class="main">
            <form method = "post" enctype=multipart/form-data>
                <div class="field">
                    <label for="n">Ваше iмя<span>*</span></label>
                    <input type="text" name="name" id="n" value="<?php echo $name;?>"><br><br>
                </div>
                <div class="field">
                    <label for="m">E-mail<span>*</span></label>
                    <input type="text" name="mail" id="m" value="<?php echo $mail;?>"><br><br>
                </div>
                <div class="field">
                    <label for="t">Номер телефону<span>*</span></label>
                    <input type="text" name="phone" id="t" value="<?php echo $phone;?>"><br><br>
                </div>
                <div class="field">
                    <label for="ph1">Вашi фото(jpg, JPG, JPEG, bmp,BMP,gif, GIF, png, PNG)<span>*</span></label>
                    <input type="file" id="ph1" accept="image/*,image/jpeg" name="filename1"><br><br>
                </div>
                <div class="field">
                    <label for="np">Опис проблеми<span>*</span></label>
                    <textarea name="comment" cols="50" rows="6" id="p"><?php echo $comment;?></textarea><br><br>
                </div>
                <div class="btn">
                    <button type="submit">Вiдправити</button><br>
                </div>
                <div class="msg">
                    <?php 
                        echo $msg;
                    ?>
                </div>
           </form>
        </div>
    </body>
</html>
