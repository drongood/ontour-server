<?php
//$uploaddir = 'images/';
// это папка, в которую будет загружаться картинка
//$apend = date('YmdHis').rand(100,1000).'.jpg';
// это имя, которое будет присвоенно изображению 
//$filetoserver = "$uploaddir$apend";
//в переменную $uploadfile будет входить папка и имя изображения
if (move_uploaded_file($_FILES['userfile']['tmp_name'], $filetoserver))
    throw new exeption('it work');
else 
throw new exeption("it isn't work");
