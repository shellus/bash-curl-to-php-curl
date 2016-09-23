<?php
/**
 * Created by PhpStorm.
 * User: shellus-out
 * Date: 2016/9/22
 * Time: 15:21
 */


function parse_command_line($str){
    $arr = [];
    $arr_index = 0;

// 下一字符将转义
    $next_escape = false;

// 当前在引号内
    $in_quote = false;

// 当前值需编码ANSI
    $to_ansi = false;

    for ($i = 0; $i < strlen($str); $i++)
    {

        if(substr($str, $i, 1) === '\\'){
            // 如果是转义字符

            $arr[$arr_index] .= substr($str, ++$i, 1);
        }else{


            if(substr($str, $i, 1) === ' ' and $in_quote === false)
            {
                $arr_index++;
                continue;
            }
            elseif ($in_quote === false && substr($str, $i, 2) === '$\'')
            {
                // bash 的特殊引号，引号内转换成ANSI，双引号本地化暂未处理
                // 参见 https://github.com/int32bit/notes/blob/master/linux/bash%E5%AD%97%E7%AC%A6%E4%B8%B2%E5%89%8D%E7%BE%8E%E5%85%83%E7%AC%A6%E5%8F%B7%E7%9A%84%E4%BD%9C%E7%94%A8.md
                $i++;
                $to_ansi = true;
                $in_quote = !$in_quote;
                continue;
            }
            elseif (substr($str, $i, 1) === '\'')
            {
                if($in_quote === true && $to_ansi == true){
                    $to_ansi = ! $to_ansi;
                }
                $in_quote = ! $in_quote;
                continue;
            }else
            {
                $char = substr($str, $i, 1);
                if($to_ansi){
                    $char = mb_convert_encoding($char, 'ASCII');
                }

                key_exists($arr_index, $arr) or $arr[$arr_index] = '';
                $arr[$arr_index] .= $char;
            }
        }
    }

    $result = [
        // ls
        'command' => [],
        // ls /usr
        'arguments' => [],
        // -user root
        'options' => [],
        // --help
        'switches' => [],
    ];

    for ($i = 0; $i < count($arr); $i++)
    {
        if (substr($arr[$i],0,2) === '--' or substr($arr[$i],0,1) === '-'){

            if(count($arr) === $i + 1 or substr($arr[$i + 1],0,2) === '--' or substr($arr[$i + 1],0,1) === '-')
            {
                // 最后一个，或者下一个值是option
                $result['options'][$arr[$i]] = true;
            }
            else
            {
                // 下一个成员作为选项值，下移数字指针
                if (key_exists($arr[$i], $result['options']))
                {
                    if (is_array($result['options'][$arr[$i]]))
                    {
                        $result['options'][$arr[$i]][] = $arr[++$i];
                    }else
                    {
                        $result['options'][$arr[$i]] = [$result['options'][$arr[$i]], $arr[++$i]];
                    }

                }else
                {
                    $result['options'][$arr[$i]] = $arr[++$i];
                }

            }
        }
        else
        {
            if($i === 0){
                $result['command'] = $arr[$i];
            }
            else
            {
                $result['arguments'][] = $arr[$i];
            }
        }
    }

    return $result;
}




