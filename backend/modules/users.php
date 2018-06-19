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

    public static function ChangeUserProfile($id, $data)
    {
        // Подключаемся к базе
        $db = Core::DB();
        $mass = $db->where('id', $id)->get('user');
        $res['phone'] = ($data['phone'] !== $mass['phone']) ? Utils::FormatPhone($data['phone']) : $mass['phone'];
        $res['name'] = ($data['name'] !== $mass['name']) ? $data['name'] : $mass['name'];
        $res['email'] = ($data['email'] !== $mass['email']) ? $data['email'] : $mass['email'];
        $res['school'] = ($data['school'] !== $mass['school']) ? $data['school'] : $mass['school'];
        $res['age'] = ($data['age'] !== $mass['age']) ? $data['age'] : $mass['age'];
        $db->where('id', $id)->update('user', $res);
        return $res;
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