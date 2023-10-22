<?php
/**
 * --------------------------------
 * # 阿拉伯数字转化为汉字大写钱数
 * --------------------------------
 * 1. 目的：防止轻易伪造纸质文档中的数字
 * 2. 数字范围 0-100 万亿，同时支持负数
 * --------------------------------
 */
namespace Swordying;

class num2rmb
{
    ## 数字与汉字对照表
    private $tradition_numbers = array(
        0 => '零',
        1 => '壹',
        2 => '贰',
        3 => '叁',
        4 => '肆',
        5 => '伍',
        6 => '陆',
        7 => '柒',
        8 => '捌',
        9 => '玖',
    );
    ## 中国计数进制换算单位
    protected $number_units = array(
        0 => '',
        1 => '拾',
        2 => '佰',
        3 => '仟',
    );
    ## 人民币单位
    protected $rmb_units = array(
        0 => '分',
        1 => '角',
        2 => '元'
    );
    ## 4 位一组进制单位
    protected $four_units = array(
        0 => '元',
        1 => '万',
        2 => '亿',
        3 => '兆', # 万亿
    );
    ## 人民币开始标识
    public $start_mark = '人民币';
    ## 结束标识
    public $end_mark = '整';
    ## 正负标识
    public $negative_mark = '负';
    // 构造函数
    public function __construct(array $config = [])
    {
        if(isset($config['start_mark'])){
            $this -> start_mark = $config['start_mark'];
        }
        if(isset($config['end_mark'])){
            $this -> end_mark = $config['end_mark'];
        }
        if(isset($config['negative_mark'])){
            $this -> negative_mark = $config['negative_mark'];
        }
    }
    public function handle($number)
    {
        $number = (float) $number;
        $number = number_format($number,2,'.','');
        if($number == 0){
            return $this -> start_mark.$this -> tradition_numbers[0].$this -> rmb_units[2].$this -> end_mark;
        }
        $negative_mark = '';
        if($number < 0){
            $negative_mark = $this -> negative_mark;
            $number = abs($number);
        }
        // 分为整数部分小数部分
        $number_array = explode('.',$number);
        // 整数部分数字
        $number_int = strrev($number_array[0]);
        // 整数部分数字长度
        $number_int_length = strlen($number_int);
        if($number_int_length > 15){
            return false; # '数字过大'
        }
        ## 声明钱数小数部分
        $tradition_float = '';
        if($number_array[1][0] == 0 && $number_array[1][1] == 0){
            $tradition_float = $this -> end_mark;
        }else{
            if($number_array[1][0] > 0){
                $jiao = $this -> tradition_numbers[$number_array[1][0]].$this -> rmb_units[1];
            }else{
                $jiao = $this -> tradition_numbers[0];
            }
            if($number_array[1][1] > 0){
                $fen = $this -> tradition_numbers[$number_array[1][1]].$this -> rmb_units[0];
            }else{
                $fen = $this -> end_mark;
            }

            $tradition_float = $jiao.$fen;
        }

        if($number_int == 0){
            return $this -> start_mark . $negative_mark . $this -> tradition_numbers[0].$this -> rmb_units[2] . $tradition_float;
        }

        // 四位一组
        $number_int_group = [];
        $j = 0;
        for ($i = 0 ; $i < $number_int_length; $i++){
            if($i % 4 == 0 && $i != 0){
                $j ++;
            }
            $number_int_group[$j][] = (int) $number_int[$i];
        }

        // 转化后的整数部分
        $tradition_int = '';
        foreach($number_int_group as $k => $v){
            $group_unit = $this -> four_units[$k];
            $group_sum = array_sum($v);

            if($group_sum == 0){
                if($k == 0){
                    $tradition_int = $this -> rmb_units[2];
                }
                continue;
            }
            $current_grpup_int = '';
            foreach($v as $key => $value){
                $current_tradition_number = $this -> tradition_numbers[$value];
                $current_number_unit = '';
                if($key == 0 && $value == 0){
                    $current_tradition_number = '';
                }
                if($value){
                    $current_number_unit = $this -> number_units[$key];
                }
                $current_grpup_int = $current_tradition_number . $current_number_unit . $current_grpup_int;
            }
            $tradition_int = $current_grpup_int . $group_unit . $tradition_int;
        }
        // 去除多余的字符
        $tradition_int = str_replace(array(
            '零零零',
            '零零',
            '零亿',
            '零万',
            '零元',
            '零兆',
            '兆',
        ),array(
            '零',
            '零',
            '亿',
            '万',
            '元',
            '万亿',
            '万亿',
        ),$tradition_int);
        $result = $this -> start_mark . $negative_mark . $tradition_int . $tradition_float;
        return $result;
    }

}
