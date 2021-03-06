<?php
/**
 * 公共函数
 * User: dxk
 * Date: 2020/11/19
 */
if (!function_exists('alert_info')) {
    /**
     * 信息返回
     * @param int $code 错误码 0成功，其他失败
     * @param string $msg 返回消息
     * @param array $data 返回数据
     * @return array
     */
    function alert_info(int $code = 0, string $msg = '', array $data = [])
    {
        return ['code' => $code, 'msg' => $msg, 'data' => $data];
    }
}
if (!function_exists('obj_to_array')) {
    /**
     * 对象转换为数组
     * @param mixed $obj
     * @return array
     */
    function obj_to_array($obj): array
    {
        if (!is_object($obj) && !is_array($obj)) {
            return [];
        }
        return json_decode(json_encode($obj), true);
    }
}
if (!function_exists('easy_get_field')) {
    /**
     * 获取数组中的指定字段
     * @param array $row
     * @param $field
     * @return array
     */
    function easy_get_field(array $row, $field): array
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
}
if (!function_exists('easy_array_get_field')) {
    /**
     * 获取数组中的指定字段
     * @param array $data_list
     * @param string|array $field
     * @param string $key_field
     * @return array
     */
    function easy_array_get_field(array $data_list, $field, string $key_field = ''): array
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
}
if (!function_exists('easy_array_sort')) {
    /**
     * 二维数组排序
     * @param array $data_list
     * @param string $sort_field
     * @param string $sort
     * @return array
     */
    function easy_array_sort(array $data_list, string $sort_field, string $sort = 'SORT_DESC'): array
    {
        $sort_field_values = [];
        foreach ($data_list as $k => $item) {
            $sort_field_values[$k] = $item[$sort_field];
        }
        array_multisort($sort_field_values, constant($sort), $data_list);
        return $data_list;
    }
}
if (!function_exists('easy_mk_dir')) {
    /**
     * 递归创建目录
     * @param string $dir
     * @param int $mode
     * @return bool
     */
    function easy_mk_dir(string $dir, $mode = 0777)
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
}
if (!function_exists('easy_del_dir')) {
    /**
     * 递归删除目录下所有文件
     * @param string $path
     */
    function easy_del_dir(string $path)
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
}
if (!function_exists('easy_microtime')) {
    /**
     * 获取当前时间
     * @return float
     */
    function easy_microtime()
    {
        list($usec, $sec) = explode(' ', microtime());
        return (float)$usec + (float)$sec;
    }
}
if (!function_exists('easy_url_params')) {
    /**
     * 获取url的参数
     * @param string $url
     * @return array
     */
    function easy_url_params(string $url): array
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
}
if (!function_exists('easy_string_to_num')) {
    /**
     * 字符串转十进制数字，默认为64进制字符串，其他进制可使用base_convert函数
     * @param string $string
     * @param string $pool
     * @return bool|float|int
     */
    function easy_string_to_num(string $string, string $pool = '')
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
}
if (!function_exists('easy_num_to_string')) {
    /**
     * 十进制数字转字符串，默认为64进制字符串，其他进制可使用base_convert函数
     * @param int $num
     * @param string $pool
     * @return string
     */
    function easy_num_to_string(int $num, string $pool = ''): string
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
}
if (!function_exists('easy_random')) {
    /**
     * 产生随机字符串
     * @param int $length 输出长度
     * @param string $chars 可选的 ，默认为 0123456789
     * @return string 字符串
     */
    function easy_random(int $length, string $chars = '0123456789'): string
    {
        $hash = '';
        $max = strlen($chars) - 1;
        for ($i = 0; $i < $length; $i++) {
            $hash .= $chars[mt_rand(0, $max)];
        }
        return $hash;
    }
}
if (!function_exists('easy_curl_post')) {
    /**
     * curl_post请求
     * @param string $url
     * @param array|string $post_data
     * @param int $timeout
     * @param array $headers
     * @return bool|string
     * @throws Exception
     */
    function easy_curl_post(string $url, $post_data = [], int $timeout = 20, array $headers = [])
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
}
if (!function_exists('easy_curl_get')) {
    /**
     * curl_get请求
     * @param string $url
     * @param array $get_data
     * @param int $timeout
     * @param array $headers
     * @return bool|string
     * @throws Exception
     */
    function easy_curl_get(string $url, array $get_data = [], int $timeout = 20, array $headers = [])
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
}
if (!function_exists('easy_app_filter_var')) {
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
}
if (!function_exists('easy_ip')) {
    /**
     * 获取请求ip
     * @return string
     */
    function easy_ip(): string
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
}
if (!function_exists('easy_float_eq')) {
    /**
     * 浮点数比较 1 == 1
     * @param float $f1
     * @param float $f2
     * @param int $precision
     * @return bool
     */
    function easy_float_eq($f1, $f2, int $precision = 4): bool
    {
        $res = easy_bccomp($f1, $f2, $precision);
        if ($res === 0) {
            return true;
        } else {
            return false;
        }
    }
}
if (!function_exists('easy_float_gt')) {
    /**
     * 浮点数比较 1 > 0
     * @param float $big
     * @param float $small
     * @param int $precision
     * @return bool
     */
    function easy_float_gt($big, $small, int $precision = 4)
    {
        $res = easy_bccomp($big, $small, $precision);
        if ($res === 1) {
            return true;
        } else {
            return false;
        }
    }
}
if (!function_exists('easy_float_ge')) {
    /**
     * 浮点数比较 2>=1
     * @param float $big
     * @param float $small
     * @param int $precision
     * @return bool
     */
    function easy_float_ge($big, $small, $precision = 4)
    {
        $res = easy_bccomp($big, $small, $precision);
        if ($res !== -1) {
            return true;
        } else {
            return false;
        }
    }
}
if (!function_exists('easy_bcadd')) {
    /**
     * 2个任意精度数字的加法计算
     * @param string $left_operand 左操作数，字符串类型
     * @param string $right_operand 右操作数，字符串类型
     * @param int $scale 此可选参数用于设置结果中小数点后的小数位数
     * @return string
     */
    function easy_bcadd($left_operand, $right_operand, int $scale = 14): string
    {
        return easy_bc($left_operand, $right_operand, $scale, 'bcadd');
    }
}
if (!function_exists('easy_bcsub')) {
    /**
     * 2个任意精度数字的减法计算
     * @param string $left_operand 左操作数，字符串类型
     * @param string $right_operand 右操作数，字符串类型
     * @param int $scale 此可选参数用于设置结果中小数点后的小数位数
     * @return string
     */
    function easy_bcsub($left_operand, $right_operand, int $scale = 14): string
    {
        return easy_bc($left_operand, $right_operand, $scale, 'bcsub');
    }
}
if (!function_exists('easy_bcmul')) {
    /**
     * 2个任意精度数字乘法计算
     * @param string $left_operand 左操作数，字符串类型
     * @param string $right_operand 右操作数，字符串类型
     * @param int $scale 此可选参数用于设置结果中小数点后的小数位数
     * @return string
     */
    function easy_bcmul($left_operand, $right_operand, int $scale = 14): string
    {
        return easy_bc($left_operand, $right_operand, $scale, 'bcmul');
    }
}
if (!function_exists('easy_bcdiv')) {
    /**
     * 2个任意精度数字除法计算
     * @param string $left_operand 左操作数，字符串类型
     * @param string $right_operand 右操作数，字符串类型
     * @param int $scale 此可选参数用于设置结果中小数点后的小数位数
     * @return string
     */
    function easy_bcdiv($left_operand, $right_operand, int $scale = 14): string
    {
        return easy_bc($left_operand, $right_operand, $scale, 'bcdiv');
    }
}
if (!function_exists('easy_bccomp')) {
    /**
     * 比较两个任意精度的数字
     * 如果两个数相等返回0, 左边的数left_operand比较右边的数right_operand大返回1, 否则返回-1.
     * @param string $left_operand 左操作数，字符串类型
     * @param string $right_operand 右操作数，字符串类型
     * @param int $scale 此可选参数用于设置结果中小数点后的小数位数
     * @return int
     */
    function easy_bccomp($left_operand, $right_operand, int $scale = 10): int
    {
        $left_operand = number_format((float)$left_operand, $scale, '.', '');
        $right_operand = number_format((float)$right_operand, $scale, '.', '');
        return bccomp($left_operand, $right_operand, $scale);
    }
}
if (!function_exists('easy_bc')) {
    /**
     * bcadd — 2个任意精度数字的加法计算
     * bccomp — 比较两个任意精度的数字
     * bcdiv — 2个任意精度的数字除法计算
     * bcmod — 对一个任意精度数字取模
     * bcmul — 2个任意精度数字乘法计算
     * bcpow — 任意精度数字的乘方
     * bcpowmod — Raise an arbitrary precision number to another, reduced by a specified modulus
     * bcscale — 设置所有bc数学函数的默认小数点保留位数
     * bcsqrt — 任意精度数字的二次方根
     * bcsub — 2个任意精度数字的减法
     * @param string $left_operand 左操作数，字符串类型
     * @param string $right_operand 右操作数，字符串类型
     * @param int $scale 此可选参数用于设置结果中小数点后的小数位数
     * @param string $method
     * @return string
     */
    function easy_bc($left_operand, $right_operand, int $scale = 10, string $method = 'bcadd')
    {
        $methods = ['bcadd', 'bccomp', 'bcdiv', 'bcmod', 'bcmul', 'bcpow', 'bcpowmod', 'bcscale', 'bcsqrt', 'bcsub'];
        if (in_array($method, $methods)) {
            $left_operand = number_format((float)$left_operand, $scale, '.', '');
            $right_operand = number_format((float)$right_operand, $scale, '.', '');
            return $method($left_operand, $right_operand, $scale);
        } else {
            return '';
        }
    }
}
if (!function_exists('easy_trim_print')) {
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
}
if (!function_exists('easy_trim_csv')) {
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
}
if (!function_exists('easy_is_empty_row')) {
    /**
     * 检测是否为空的excel单元行
     * @param array $row
     * @return bool
     */
    function easy_is_empty_row(array $row): bool
    {
        foreach ($row as $item) {
            if (!is_object($item) && !empty(trim($item))) {
                return false;
            }
        }
        return true;
    }
}
if (!function_exists('easy_gbk_to_utf8')) {
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
}
if (!function_exists('easy_utf8_to_gbk')) {
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
}
if (!function_exists('easy_get_valid_field')) {
    /**
     * 获取有效字段
     * @param array $user_fields 用户输入字段
     * @param array $allow_fields 系统允许字段
     * @return array
     */
    function easy_get_valid_field(array $user_fields, array $allow_fields): array
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
}
if (!function_exists('easy_make_sign')) {
    /**
     * 生成签名
     * @param array $params
     * @param string $app_secret
     * @return string
     */
    function easy_make_sign(array $params, string $app_secret): string
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
}
if (!function_exists('easy_check_sign')) {
    /**
     * 验证签名
     * @param array $params
     * @param string $app_secret
     * @param string $sign_key
     * @return bool
     */
    function easy_check_sign(array $params, string $app_secret, string $sign_key = 'sign'): bool
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
}
if (!function_exists('easy_get_current_url')) {
    /**
     * 获取当前连接地址
     * @return string
     */
    function easy_get_current_url(): string
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (int)$_SERVER['SERVER_PORT'] === 443) || strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) == 'https' ? 'https://' : 'http://';
        return $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }
}
if (!function_exists('easy_p')) {
    /**
     * 打印信息
     * @param mixed ...$vars
     */
    function easy_p(...$vars)
    {
        foreach ($vars as $var) {
            echo '<pre>';
            print_r($var);
            echo '</pre>';
        }
        die;
    }
}
if (!function_exists('easy_is_mobile')) {
    /**
     * 判断是否为手机访问
     * @return bool
     */
    function easy_is_mobile(): bool
    {
        $regex_match = '/(nokia|iphone|android|motorola|^mot\-|softbank|foma|docomo|kddi|up\.browser|up\.link|';
        $regex_match .= 'htc|dopod|blazer|netfront|helio|hosin|huawei|novarra|CoolPad|webos|techfaith|palmsource|';
        $regex_match .= 'blackberry|alcatel|amoi|ktouch|nexian|samsung|^sam\-|s[cg]h|^lge|ericsson|philips|sagem|wellcom|bunjalloo|maui|';
        $regex_match .= 'symbian|smartphone|midp|wap|phone|windows ce|iemobile|^spice|^bird|^zte\-|longcos|pantech|gionee|^sie\-|portalmmm|';
        $regex_match .= 'jig\s browser|hiptop|^ucweb|^benq|haier|^lct|opera\s*mobi|opera\*mini|320x320|240x320|176x220';
        $regex_match .= ')/i';
        return isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE']) or preg_match($regex_match, strtolower($_SERVER['HTTP_USER_AGENT']));
    }
}
if (!function_exists('easy_is_alipay')) {
    /**
     * 判断是否为支付宝
     * @return bool
     */
    function easy_is_alipay(): bool
    {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'AlipayClient') !== false) {
            return true;
        }
        return false;
    }
}
if (!function_exists('easy_is_wechat')) {
    /**
     * 判断是否为微信
     * @param string $user_agent
     * @return bool
     */
    function easy_is_wechat($user_agent = ''): bool
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
}
if (!function_exists('easy_get_wechat_version')) {
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
}
if (!function_exists('easy_is_alipay_code')) {
    /**
     * 检测是否支付宝刷卡条码 25~30开头的长度为16~24位的数字，实际字符串长度以开发者获取的付款码长度为准
     * @param string $barcode
     * @return bool
     */
    function easy_is_alipay_code($barcode): bool
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
}
if (!function_exists('easy_is_wechatpay_code')) {
    /**
     * 检测是否微信刷卡条码：18位纯数字，以10、11、12、13、14、15开头
     * @param string $barcode
     * @return bool
     */
    function easy_is_wechatpay_code($barcode): bool
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
}
if (!function_exists('easy_get_browser_version')) {
    /**
     * 获取浏览器以及版本号
     * @param string $user_agent
     * @return array
     */
    function easy_get_browser_version(string $user_agent = ''): array
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
}
if (!function_exists('easy_in_array')) {
    /**
     * 判断一个数组中的其中一个值是否在数组中
     * @param string|array $needles
     * @param array $haystack
     * @return bool
     */
    function easy_in_array($needles, array $haystack): bool
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
}
if (!function_exists('easy_check_change_data')) {
    /**
     * 检测发生变化的数据
     * @param array $old_data
     * @param array $new_data
     * @return array
     */
    function easy_check_change_data(array $old_data = [], array $new_data = []): array
    {
        $change_data = [];
        foreach ($old_data as $k => $item) {
            if (isset($new_data[$k]) && $new_data[$k] != $item) {
                $change_data[$k] = ['old' => $item, 'new' => $new_data[$k]];
            }
        }
        return $change_data;
    }
}
if (!function_exists('easy_get_change_str')) {
    /**
     * 获取变化内容
     * @param array $change_data
     * @return string
     */
    function easy_get_change_str(array $change_data)
    {
        $str = '';
        unset($change_data['operate_id'], $change_data['operate_by']);
        foreach ($change_data as $k => $v) {
            $str .= $k . ':' . $v['old'] . ' => ' . $v['new'];
        }
        return $str;
    }
}
if (!function_exists('easy_weight_with_use_num')) {
    /**
     * 按照权重获取随机的key
     * @param array $data_list
     * @param string $weight_field
     * @param string $use_field
     * @return bool|string
     */
    function easy_weight_with_use_num(array $data_list, $weight_field = 'weight', $use_field = 'use_num')
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
}