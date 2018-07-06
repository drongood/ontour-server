<?php

class Users
{
    const ROLE_USER = 'user';
    const ROLE_ADMIN = 'admin';

    const STATUS_NEW = 0;
    const STATUS_APPROVED = 1;
    const STATUS_DISABLED = 2;

    public static function CheckUserData($data)
    {
        if(!isset($data['password']) || strlen($data['password']) < 8 || strlen($data['password']) > 255)
            throw new \Exception("Пароль должен быть от 8 до 255 символов");

        // TODO нормальная проверка email-a
        if(!isset($data['email']) || empty($data['email']) || stripos($data['email'], '@') == false)
            throw new \Exception("emal отсутствует или указан неверно");

        if(!isset($data['name']) || empty($data['name']))
            throw new \Exception("Не указано имя");

        if(!isset($data['phone']) || strlen($data['phone']) != 10)
            throw new \Exception("Не указан телефон");
    }

    public static function RegisterUser($data)
    {
        $data['phone'] = Utils::FormatPhone($data['phone']);
        self::CheckUserData($data);
        // Подключаемся к базе
        $db = Core::DB();
        // Проверка по имени телефона
        $res = $db->where('phone', $data['phone'])->get('user');
        if(!empty($res)) {
            throw new Exception('Пользователь с указанным телефоном уже существует');
        }
        $res = $db->where('email', $data['email'])->get('user');
        if(!empty($res)) {
            throw new Exception('Пользователь с указанной почтой уже существует');
        }
        $user_data = array(
            'password'  => md5($data['password']),
            'phone'     => $data['phone'],
            'email'     => $data['email'],
            'role'      => self::ROLE_USER,
            'date_reg'  => time(),
            'date_last' => time(),
            'status'    => self::STATUS_NEW,
            'rating'    => 0,
            /* TODO Если регистрация по приглашению, то возможна установка начальных баллов */
            /* и установка баллов для того, кто пригласил */
            'score'     => 0,
            'name'      => $data['name'],
        );

        $db->insert('user', $user_data);
        $new_id = $db->getInsertId();
        if($new_id > 0)
            return $new_id;
        else
            throw new Exception('Непредвиденная ошибка при регистрации пользователя');
    }

    public static function ChangePass($id, $pass_old, $pass_new){
        $db = Core::DB();
        if(!isset($pass_old) || strlen($pass_old) < 8 || strlen($pass_old) > 255)
            throw new \Exception("Пароль должен быть от 8 до 255 символов");
        if(!isset($pass_new) || strlen($pass_new) < 8 || strlen($pass_new) > 255)
            throw new \Exception("Пароль должен быть от 8 до 255 символов");

        $pass_new = md5($pass_new);
        $pass_old = md5($pass_old);

        $res = $db -> where('id', $id);
        if (isset($res) && !empty($res)){
            if ($pass_old == $res['password']){
                $upd = array(
                    'password' => $pass_new,
                    'phone'    => $res['phone'],
                    'email'    => $res['email'],
                    'name'     => $res['name'],
                    'school'   => $res['school'],

                );
            }
            else
                throw new Exception('Неверно введен старый пароль');
        }
        $db -> where('id', $id)
            -> update('user', $upd);

        return Core::DB() -> getLastError();
    }

    public static function ChangeUserProfile($id, $data)
    {
        // Подключаемся к базе
        $db = Core::DB();
        $upd = array();
        if(isset($data['phone']) && !empty($data['phone'])) {
            $data['phone'] = Utils::FormatPhone($data['phone']);
            $res = $db->where('phone', $data['phone'])->where('id', $id, '!=')->get('user');
            if(!empty($res)) {
                throw new Exception('Указанный телефон занят другим пользователем');
            }
            $upd['phone'] = $data['phone'];
        }
        if(isset($data['name'])) {
            $data['name'] = trim($data['name']);
            if(empty($data['name']))
                throw new Exception('Имя пользователя не может быть пустым');
            $upd['name'] = $data['name'];
        }
        if(isset($data['email']) && !empty($data['email'])) {
            $res = $db->where('email', $data['email'])->where('id', $id, '!=')->get('user');
            if(!empty($res)) {
                throw new Exception('Указанный email занят другим пользователем');
            }
            $upd['email'] = $data['email'];
        }
        if(isset($data['age']) && !empty($data['age'])) {
            $upd['age'] = $data['age'];
        }
        if(isset($data['school']) && !empty($data['school'])) {
            $upd['school'] = $data['school'];
        }
        $db->where('id', $id)->update('user', $upd);
        if($msg = $db->getLastError())
            throw new Exception('Непредвиденная ошибка при сохранении данных.'.(Config::DEBUG ? ' '.$msg : ''));
        else
            return true;
    }

    public static function CheckUserCredentials($login, $password)
    {
        // Подключаемся к базе
        $db = Core::DB();
        // Ищем пользователя
        if($ph = Utils::FormatPhone($login)) {
            $res = $db
                ->where('phone', $ph)
                ->where('password', md5($password))
                ->get('user');
        } else {
            $res = $db
                ->where('email', $login)
                ->where('password', md5($password))
                ->get('user');
        }
        if(sizeof($res) == 1) {
            return $res[0]['id'];
        } else {
            return null;
        }
    }

    public static function GetUserInfo($userid)
    {
        $res = Core::DB()->where('id', $userid)->getOne('user');
        return $res;
    }

    public static function UpdateLastActive($userid, $time)
    {
        Core::DB()->where('id', $userid)->update('user', array(
            'date_last' => time(),
        ));
        return true;
    }
}