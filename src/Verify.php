<?php
/**
 * 常用正则验证类
 * User: dxk
 * Date: 2020/11/19
 */
namespace EasyHelper;
class Verify
{
    /**
     * 验证用户名
     * 字母、数字、下划线、汉字组成,2-20位字符,不能为纯数字
     *
     * @param $username
     * @param int $min_len
     * @param int $max_len
     * @return bool|int
     */
    public static function isUserName($username, $min_len = 2, $max_len = 20)
    {
        if (empty($username)) {
            return false;
        }
        $match = '/^(?![0-9]+$)[\w\x{4e00}-\x{9fa5}]{' . $min_len . ',' . $max_len . '}$/iu';
        return preg_match($match, $username);
    }

    /**
     * 验证用户名
     * @param $name
     * @param int $min_len
     * @param int $max_len
     * @param string $charset
     * @return bool|int
     */
    public static function isName($name, $min_len = 2, $max_len = 20, $charset = 'ALL')
    {
        if (empty($name)) {
            return false;
        }
        switch ($charset) {
            case 'EN':
                $match = '/^[_\w\d]{' . $min_len . ',' . $max_len . '}$/iu';
                break;
            case 'CN':
                $match = '/^[_\x{4e00}-\x{9fa5}\d]{' . $min_len . ',' . $max_len . '}$/iu';
                break;
            default:
                $match = '/^[_\w\d\x{4e00}-\x{9fa5}]{' . $min_len . ',' . $max_len . '}$/iu';
        }
        return preg_match($match, $name);
    }

    /**
     * 密码提示信息
     * @var string
     */
    static $password_tip = '密码必须为6-20位的英文字母、数字或符号，不能是纯数字';

    /**
     * 验证密码格式
     * 6-20个英文字母、数字或符号，不能是纯数字
     *
     * @param $password
     * @param int $min_len
     * @param int $max_len
     * @return bool|int
     */
    public static function isPassword($password, $min_len = 6, $max_len = 20)
    {
        $match = '/^[\\~!@#$%^&*()-_=+|{}\[\],.?\/:;\'\"\d\w]{' . $min_len . ',' . $max_len . '}$/';
        $password = trim($password);
        if (empty($password)) {
            return false;
        }
        if (preg_match('/^[\d]*$/', $password)) {
            return false;
        }
        return preg_match($match, $password);
    }

    /**
     * 验证eamil格式
     * @param $email
     * @param string $match
     * @return bool|int
     */
    public static function isEmail($email, $match = '/^[\w\d]+[\w\d\-.]*@[\w\d\-.]+\.[\w\d]{2,10}$/i')
    {
        $email = trim($email);
        if (empty($email)) {
            return false;
        }
        return preg_match($match, $email);
    }

    /**
     * 验证电话号码
     * @param $telephone
     * @param string $match
     * @return bool|int
     */
    public static function isTelephone($telephone, $match = '/^0[0-9]{2,3}[-]?\d{7,8}$/')
    {
        $telephone = trim($telephone);
        if (empty($telephone)) {
            return false;
        }
        return preg_match($match, $telephone);
    }

    /**
     * 验证手机号
     * @param $mobile
     * @param string $match
     * @return bool|int
     */
    public static function isMobile($mobile, $match = '/^((\+86)?(1[1|2|3|4|5|6|7|8|9]\d{9}))$/')
    {
        $mobile = trim($mobile);
        if (empty($mobile)) {
            return false;
        }
        return preg_match($match, $mobile);
    }

    /**
     * 验证邮政编码
     * @param $postcode
     * @param string $match
     * @return bool|int
     */
    public static function isPostcode($postcode, $match = '/\d{6}/')
    {
        $postcode = trim($postcode);
        if (empty($postcode)) {
            return false;
        }
        return preg_match($match, $postcode);
    }

    /**
     * 验证IP
     * @param $ip
     * @param string $match
     * @return bool|int
     */
    public static function isIp($ip, $match = '/^(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9])\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[0-9])$/')
    {
        $ip = trim($ip);
        if (empty($ip)) {
            return false;
        }
        return preg_match($match, $ip);
    }

    /**
     * 验证身份证号码
     * @param $idcard
     * @param string $match
     * @return bool|int
     */
    public static function isIdcard($idcard, $match = '/^\d{6}((1[89])|(2\d))\d{2}((0\d)|(1[0-2]))((3[01])|([0-2]\d))\d{3}(\d|X)$/i')
    {
        $idcard = trim($idcard);
        if (empty($idcard)) {
            return false;
        } else if (strlen($idcard) > 18) {
            return false;
        }
        return preg_match($match, $idcard);
    }

    /**
     * 验证域名
     * @param $domain
     * @param string $match
     * @return bool|int
     */
    public static function isDomain($domain, $match = '/^[a-zA-Z0-9][-a-zA-Z0-9]{0,62}(\.[a-zA-Z0-9][-a-zA-Z0-9]{0,62})+\.?$/')
    {
        $domain = strtolower(trim($domain));
        if (empty($domain)) {
            return false;
        }
        return preg_match($match, $domain);
    }

    /**
     * 验证URL
     * @param $url
     * @param string $match
     * @return bool|int
     */
    public static function isUrl($url, $match = '/^http(s?):\/\/(?:[A-za-z0-9-]+\.)+[A-za-z]{2,4}(:\d+)?(?:[\/\?#][\/=\?%\-&~`@[\]\':+!\.#\w]*)?/')
    {
        $url = strtolower(trim($url));
        if (empty($url)) {
            return false;
        }
        return preg_match($match, $url);
    }

    /**
     * 验证qq
     * 5-11位数字
     *
     * @param $qq
     * @param int $min_len
     * @param int $max_len
     * @return bool|int
     */
    public static function isQq($qq, $min_len = 5, $max_len = 11)
    {
        if (empty($qq)) {
            return false;
        }
        $match = '/^([1-9]\d{' . $min_len . ',' . $max_len . '})$/';
        return preg_match($match, $qq);
    }
}