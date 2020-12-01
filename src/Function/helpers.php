<?php
/**
 * 公共函数
 * User: dxk
 * Date: 2020/11/19
 */
/**
 * 信息返回
 * @param int $code 错误码 0成功，其他失败
 * @param string $msg 返回消息
 * @param array $data 返回数据
 * @return array
 */
function alert_info($code = 0, $msg = '', $data = [])
{
    return ['code' => $code, 'msg' => $msg, 'data' => $data];
}

/**
 * 对象转换为数组
 * @param mixed $obj
 * @return array
 */
function obj_to_array($obj)
{
    if (!is_object($obj) && !is_array($obj)) {
        return [];
    }
    return json_decode(json_encode($obj), true);
}

/**
 * 获取数组中的指定字段
 * @param array $row
 * @param $field
 * @return array
 */
function easy_get_field($row, $field)
{
    if (!is_array($row)) {
        return [];
    }
    if (empty($field) || $field == '*' || (is_array($field) && in_array('*', $field))) {
        return $row;
    }
    if (!is_array($field)) {
        $field = [$field];
    }
    foreach ($row as $k => $item) {
        if (!in_array($k, $field)) {
            unset($row[$k]);
        }
    }
    return $row;
}

/**
 * 获取数组中的指定字段
 * @param array $data_list
 * @param string|array $field
 * @param string $key_field
 * @return array
 */
function easy_array_get_field($data_list, $field, $key_field = '')
{
    $list = [];
    foreach ($data_list as $item) {
        if (empty($key_field)) {
            $list[] = easy_get_field($item, $field);
        } else {
            $list[$item[$key_field]] = easy_get_field($item, $field);
        }
    }
    return $list;
}

/**
 * 二维数组排序
 * @param array $data_list
 * @param string $sort_field
 * @param string $sort
 * @return array
 */
function easy_array_sort($data_list, $sort_field, $sort = 'SORT_DESC')
{
    $sort_field_values = [];
    foreach ($data_list as $k => $item) {
        $sort_field_values[$k] = $item[$sort_field];
    }
    array_multisort($sort_field_values, constant($sort), $data_list);
    return $data_list;
}

/**
 * 递归创建目录
 * @param string $dir
 * @param int $mode
 * @return bool
 */
function easy_mk_dir($dir, $mode = 0777)
{
    if (is_dir($dir)) {
        return true;
    }
    if (@mkdir($dir, $mode)) {
        @chmod($dir, $mode);
        return true;
    }
    if (!easy_mk_dir(dirname($dir), $mode)) {
        return false;
    }
    $res = @mkdir($dir, $mode);
    if ($res) {
        @chmod($dir, $mode);
    }
    return $res;
}

/**
 * 递归删除目录下所有文件
 * @param string $path
 */
function easy_del_dir($path)
{
    $op = dir($path);
    while (false != ($item = $op->read())) {
        if ($item == '.' || $item == '..') {
            continue;
        }
        if (is_dir($op->path . '/' . $item)) {
            easy_del_dir($op->path . '/' . $item);
            rmdir($op->path . '/' . $item);
        } else {
            unlink($op->path . '/' . $item);
        }
    }
}

/**
 * 获取当前时间
 * @return float
 */
function easy_microtime()
{
    list($usec, $sec) = explode(' ', microtime());
    return (float)$usec + (float)$sec;
}

/**
 * 获取url的参数
 * @param string $url
 * @return array
 */
function easy_url_params($url)
{
    $url_info = parse_url($url);
    $query = $url_info['query'];
    $queryParts = explode('&', $query);
    $params = [];
    foreach ($queryParts as $param) {
        $item = explode('=', $param);
        $params[$item[0]] = $item[1];
    }
    return $params;
}

/**
 * 字符串转十进制数字，默认为64进制字符串，其他进制可使用base_convert函数
 * @param string $string
 * @param string $pool
 * @return bool|float|int
 */
function easy_string_to_num($string, $pool = '')
{
    if (empty($pool)) {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    }
    $base = strlen($pool);
    $num = 0;
    while (strlen($string) > 0) {
        $pos = strpos($pool, $string[0]);
        if ($pos === false) {
            return false;
        }
        $num += ($pos * pow($base, strlen($string) - 1));
        $string = substr($string, 1);
    }
    return $num;
}

/**
 * 十进制数字转字符串，默认为64进制字符串，其他进制可使用base_convert函数
 * @param int $num
 * @param string $pool
 * @return string
 */
function easy_num_to_string($num, $pool = '')
{
    if (empty($pool)) {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    }
    $base = strlen($pool);
    $code = "";
    while ($num > $base - 1) {
        $k = intval(fmod($num, $base));
        $code = $pool[$k] . $code;
        $num = floor($num / $base);
    }
    $code = $pool[$num] . $code;
    return $code;
}

/**
 * 产生随机字符串
 * @param int $length 输出长度
 * @param string $chars 可选的 ，默认为 0123456789
 * @return string 字符串
 */
function easy_random($length, $chars = '0123456789')
{
    $hash = '';
    $max = strlen($chars) - 1;
    for ($i = 0; $i < $length; $i++) {
        $hash .= $chars[mt_rand(0, $max)];
    }
    return $hash;
}

/**
 * curl_post请求
 * @param string $url
 * @param array|string $post_data
 * @param int $timeout
 * @param array $headers
 * @return bool|string
 * @throws Exception
 */
function easy_curl_post($url, $post_data = [], $timeout = 20, $headers = [])
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FAILONERROR, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 10);
    if (substr($url, 0, 5) == 'https') {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    }
    if (!empty($headers)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    if (is_array($post_data) && 0 < count($post_data)) {
        $postBodyString = "";
        $postMultipart = false;
        foreach ($post_data as $k => $v) {
            if ("@" != substr($v, 0, 1)) // 判断是不是文件上传
            {
                $postBodyString .= "$k=" . urlencode($v) . "&";
            } else {
                $postMultipart = true;
            }
        }
        unset($k, $v);
        curl_setopt($ch, CURLOPT_POST, true);
        if ($postMultipart) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        } else {
            curl_setopt($ch, CURLOPT_POSTFIELDS, substr($postBodyString, 0, -1));
        }
    } elseif (is_string($post_data)) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    }
    $reponse = curl_exec($ch);
    if (curl_errno($ch)) {
        throw new Exception(curl_error($ch), 500);
    } else {
        $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (200 !== $httpStatusCode) {
            throw new Exception($reponse, $httpStatusCode);
        }
    }
    return $reponse;
}

/**
 * curl_get请求
 * @param string $url
 * @param array $get_data
 * @param int $timeout
 * @param array $headers
 * @return bool|string
 * @throws Exception
 */
function easy_curl_get($url, $get_data = [], $timeout = 20, $headers = [])
{
    if (!empty($get_data)) {
        $url .= '?' . http_build_query($get_data);
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FAILONERROR, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 10);
    if (substr($url, 0, 5) == 'https') {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    }
    if (!empty($headers)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    $reponse = curl_exec($ch);
    if (curl_errno($ch)) {
        throw new Exception(curl_error($ch), 500);
    } else {
        $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (200 !== $httpStatusCode) {
            throw new Exception($reponse, $httpStatusCode);
        }
    }
    return $reponse;
}

/**
 * app过滤变量
 * @param mixed $var
 * @return array|string
 */
function easy_app_filter_var($var)
{
    if (is_object($var)) {
        return $var;
    }
    if (is_array($var)) {
        foreach ($var as $k => $item) {
            $var[$k] = easy_app_filter_var($item);
        }
        return $var;
    }
    return trim((string)$var);
}

/**
 * 获取请求ip
 * @return string
 */
function easy_ip()
{
    $ip = '';
    if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
        $ip = getenv('HTTP_CLIENT_IP');
    } elseif (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
        $ip = getenv('HTTP_X_FORWARDED_FOR');
    } elseif (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
        $ip = getenv('REMOTE_ADDR');
    } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return preg_match('/[\d.]{7,15}/', $ip, $matches) ? $matches[0] : '';
}

/**
 * 浮点数比较 1 == 1
 * @param float $f1
 * @param float $f2
 * @param int $precision
 * @return bool
 */
function easy_float_eq($f1, $f2, $precision = 4)
{
    $res = bccomp($f1, $f2, $precision);
    if ($res === 0) {
        return true;
    } else {
        return false;
    }
}

/**
 * 浮点数比较 1 > 0
 * @param float $big
 * @param float $small
 * @param int $precision
 * @return bool
 */
function easy_float_gt($big, $small, $precision = 4)
{
    $res = bccomp($big, $small, $precision);
    if ($res === 1) {
        return true;
    } else {
        return false;
    }
}

/**
 * 浮点数比较 2>=1
 * @param float $big
 * @param float $small
 * @param int $precision
 * @return bool
 */
function easy_float_ge($big, $small, $precision = 4)
{
    $res = bccomp($big, $small, $precision);
    if ($res !== -1) {
        return true;
    } else {
        return false;
    }
}

/**
 * 打印输出过滤
 * @param string|array $subject
 * @return mixed
 */
function easy_trim_print($subject)
{
    $search = [',', "'", "\r\n", "\n", "\r", "\t", "\\", '"', '(', ')'];
    $replace = ['，', '’', ' ', ' ', ' ', ' ', ' ', ' ', '（', '）'];
    return str_ireplace($search, $replace, $subject);
}

/**
 * csv导出过滤
 * @param string|array $subject
 * @return mixed
 */
function easy_trim_csv($subject)
{
    $search = ['"', "'", "\r\n", "\n", "\r", ',', "\t"];
    $replace = [' ', '’', ' ', ' ', ' ', '，', ' '];
    return str_ireplace($search, $replace, $subject);
}

/**
 * 检测是否为空的excel单元行
 * @param array $row
 * @return bool
 */
function easy_is_empty_row($row)
{
    foreach ($row as $item) {
        $item = trim($item);
        if (!empty($item)) {
            return false;
        }
    }
    return true;
}

/**
 * gbk编码转成utf8编码
 * @param string|array $var
 * @return string|array
 */
function easy_gbk_to_utf8($var)
{
    if (is_array($var)) {
        foreach ($var as $k => $str) {
            $var[$k] = easy_gbk_to_utf8($str);
        }
    } else {
        $var = mb_convert_encoding($var, 'utf-8', 'gbk');
    }
    return $var;
}

/**
 * utf-8编码转成gbk编码
 * @param string|array $var
 * @return string|array
 */
function easy_utf8_to_gbk($var)
{
    if (is_array($var)) {
        foreach ($var as $k => $str) {
            $var[$k] = easy_utf8_to_gbk($str);
        }
    } else {
        $var = mb_convert_encoding($var, 'gbk', 'utf-8');
    }
    return $var;
}

/**
 * 获取有效字段
 * @param array $user_fields 用户输入字段
 * @param array $allow_fields 系统允许字段
 * @return array
 */
function easy_get_valid_field($user_fields, $allow_fields)
{
    $user_fields = array_map('trim', $user_fields);
    $user_fields = array_map('strtolower', $user_fields);
    foreach ($user_fields as $k => $v) {
        if (empty($v)) {
            unset($user_fields[$k]);
        }
    }
    return array_intersect($user_fields, $allow_fields);
}

/**
 * 生成签名
 * @param array $params
 * @param string $app_secret
 * @return string
 */
function easy_make_sign($params, $app_secret)
{
    ksort($params);
    $stringToBeSigned = $app_secret;
    foreach ($params as $k => $v) {
        if ("@" != substr($v, 0, 1)) {
            if (!is_array($v)) {
                $v = stripslashes($v);
            }
            $stringToBeSigned .= "$k$v";
        }
    }
    unset($k, $v);
    $stringToBeSigned .= $app_secret;
    return strtoupper(md5($stringToBeSigned));
}

/**
 * 验证签名
 * @param array $params
 * @param string $app_secret
 * @param string $sign_key
 * @return bool
 */
function easy_check_sign($params, $app_secret, $sign_key = 'sign')
{
    $sign = trim($params[$sign_key]);
    unset($params[$sign_key]);
    ksort($params);
    $stringToBeSigned = $app_secret;
    foreach ($params as $k => $v) {
        if ("@" != substr($v, 0, 1)) {
            if (!is_array($v)) {
                $v = stripslashes($v);
            }
            $stringToBeSigned .= "$k$v";
        }
    }
    unset($k, $v);
    $stringToBeSigned .= $app_secret;
    $check_sign = strtoupper(md5($stringToBeSigned));
    if (strcmp($check_sign, $sign) !== 0) {
        return false;
    }
    return true;
}

/**
 * 获取当前连接地址
 * @return string
 */
function easy_get_current_url()
{
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (int)$_SERVER['SERVER_PORT'] === 443) || strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) == 'https' ? 'https://' : 'http://';
    return $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

/**
 * 打印信息
 */
function easy_p()
{
    $vars = func_get_args();
    foreach ($vars as $var) {
        echo '<pre>';
        print_r($var);
        echo '</pre>';
    }
    die;
}

/**
 * 判断是否为手机访问
 * @return bool
 */
function easy_is_mobile()
{
    $regex_match = '/(nokia|iphone|android|motorola|^mot\-|softbank|foma|docomo|kddi|up\.browser|up\.link|';
    $regex_match .= 'htc|dopod|blazer|netfront|helio|hosin|huawei|novarra|CoolPad|webos|techfaith|palmsource|';
    $regex_match .= 'blackberry|alcatel|amoi|ktouch|nexian|samsung|^sam\-|s[cg]h|^lge|ericsson|philips|sagem|wellcom|bunjalloo|maui|';
    $regex_match .= 'symbian|smartphone|midp|wap|phone|windows ce|iemobile|^spice|^bird|^zte\-|longcos|pantech|gionee|^sie\-|portalmmm|';
    $regex_match .= 'jig\s browser|hiptop|^ucweb|^benq|haier|^lct|opera\s*mobi|opera\*mini|320x320|240x320|176x220';
    $regex_match .= ')/i';
    return isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE']) or preg_match($regex_match, strtolower($_SERVER['HTTP_USER_AGENT']));
}

/**
 * 判断是否为支付宝
 * @return bool
 */
function easy_is_alipay()
{
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'AlipayClient') !== false) {
        return true;
    }
    return false;
}

/**
 * 判断是否为微信
 * @param string $user_agent
 * @return bool
 */
function easy_is_wechat($user_agent = '')
{
    if (empty($user_agent)) {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
    }
    if (strpos($user_agent, 'MicroMessenger') === false) {
        return false;
    } else {
        return true;
    }
}

/**
 * 获取微信版本号
 * @return float
 */
function easy_get_wechat_version()
{
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    preg_match('~MicroMessenger/([\d.]+)~', $user_agent, $match);
    return round($match[1], 2);
}

/**
 * 检测是否支付宝刷卡条码 25~30开头的长度为16~24位的数字，实际字符串长度以开发者获取的付款码长度为准
 * @param string $barcode
 * @return bool
 */
function easy_is_alipay_code($barcode)
{
    $barcode = trim($barcode);
    if (empty($barcode)) {
        return false;
    }
    if (!preg_match('/^(25|26|27|28|29|30)\d{14,22}$/', $barcode)) {
        return false;
    }
    return true;
}

/**
 * 检测是否微信刷卡条码：18位纯数字，以10、11、12、13、14、15开头
 * @param string $barcode
 * @return bool
 */
function easy_is_wechatpay_code($barcode)
{
    $barcode = trim($barcode);
    if (empty($barcode)) {
        return false;
    }
    if (!preg_match('/^(10|11|12|13|14|15)\d{16}$/', $barcode)) {
        return false;
    }
    return true;
}

/**
 * 获取浏览器以及版本号
 * @param string $user_agent
 * @return array
 */
function easy_get_browser_version($user_agent = '')
{
    if (empty($user_agent)) {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
    }
    $browser = '';
    $version = '';
    if (preg_match('/OmniWeb\/(v*)([^\s|;]+)/i', $user_agent, $regs)) {
        $browser = 'OmniWeb';
        $version = $regs[2];
    }
    if (preg_match('/Netscape([\d]*)\/([^\s]+)/i', $user_agent, $regs)) {
        $browser = 'Netscape';
        $version = $regs[2];
    }
    if (preg_match('/safari\/([^\s]+)/i', $user_agent, $regs)) {
        $browser = 'Safari';
        $version = $regs[1];
    }
    if (preg_match('/MSIE\s([^\s|;]+)/i', $user_agent, $regs)) {
        $browser = 'Internet Explorer';
        $version = $regs[1];
    }
    if (preg_match('/Opera[\s|\/]([^\s]+)/i', $user_agent, $regs)) {
        $browser = 'Opera';
        $version = $regs[1];
    }
    if (preg_match('/NetCaptor\s([^\s|;]+)/i', $user_agent, $regs)) {
        $browser = '(Internet Explorer ' . $version . ') NetCaptor';
        $version = $regs[1];
    }
    if (preg_match('/Maxthon/i', $user_agent, $regs)) {
        $browser = '(Internet Explorer ' . $version . ') Maxthon';
        $version = '';
    }
    if (preg_match('/360SE/i', $user_agent, $regs)) {
        $browser = '(Internet Explorer ' . $version . ') 360SE';
        $version = '';
    }
    if (preg_match('/SE 2.x/i', $user_agent, $regs)) {
        $browser = '(Internet Explorer ' . $version . ') 搜狗';
        $version = '';
    }
    if (preg_match('/FireFox\/([^\s]+)/i', $user_agent, $regs)) {
        $browser = 'FireFox';
        $version = $regs[1];
    }
    if (preg_match('/Lynx\/([^\s]+)/i', $user_agent, $regs)) {
        $browser = 'Lynx';
        $version = $regs[1];
    }
    if (preg_match('/Chrome\/([^\s]+)/i', $user_agent, $regs)) {
        $browser = 'Chrome';
        $version = $regs[1];

    }
    if ($browser != '') {
        return ['browser' => $browser, 'version' => $version];
    } else {
        return ['browser' => 'unknow browser', 'version' => 'unknow browser version'];
    }
}

/**
 * 判断一个数组中的其中一个值是否在数组中
 * @param string|array $needles
 * @param array $haystack
 * @return bool
 */
function easy_in_array($needles, $haystack)
{
    if (!is_array($needles)) {
        return in_array($needles, $haystack);
    }
    foreach ($needles as $needle) {
        if (in_array($needle, $haystack)) {
            return true;
        }
    }
    return false;
}

/**
 * 检测发生变化的数据
 * @param array $old_data
 * @param array $new_data
 * @return array
 */
function easy_check_change_data($old_data = [], $new_data = [])
{
    $change_data = [];
    foreach ($old_data as $k => $item) {
        if (isset($new_data[$k]) && $new_data[$k] != $item) {
            $change_data[$k] = ['old' => $item, 'new' => $new_data[$k]];
        }
    }
    return $change_data;
}

/**
 * 获取变化内容
 * @param array $change_data
 * @return string
 */
function easy_get_change_str($change_data)
{
    $str = '';
    unset($change_data['operate_id'], $change_data['operate_by']);
    foreach ($change_data as $k => $v) {
        $str .= $k . ':' . $v['old'] . ' => ' . $v['new'];
    }
    return $str;
}

/**
 * 按照权重获取随机的key
 * @param array $data_list
 * @param string $weight_field
 * @param string $use_field
 * @return bool|string
 */
function easy_weight_with_use_num($data_list, $weight_field = 'weight', $use_field = 'use_num')
{
    if (empty($data_list)) {
        return false;
    }
    $min_weight_use = null;
    $min_key = null;
    $weight_list = [];
    foreach ($data_list as $k => $item) {
        $use_num = isset($item[$use_field]) ? $item[$use_field] : null;
        $weight = isset($item[$weight_field]) ? $item[$weight_field] : null;
        if (!is_numeric($weight) || $weight <= 0) {
            continue;
        }
        if (!is_numeric($use_num) || $use_num <= 0) {
            $use_num = 0;
        }
        $use_num = (float)$use_num;
        $weight = (float)$weight;
        $weight_use = round($use_num / $weight, 6);
        if (is_null($min_weight_use)) {
            $min_weight_use = $weight_use;
            $min_key = $k;
        } else {
            if (easy_float_gt($min_weight_use, $weight_use, 6)) {
                $min_weight_use = $weight_use;
                $min_key = $k;
            }
        }
        $weight_list[$k] = $weight_use;
    }
    if (is_null($min_key)) {
        return false;
    }
    return $min_key;
}