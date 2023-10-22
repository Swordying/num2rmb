# 阿拉伯数字转化为汉字大写钱数

- 目的：防止轻易伪造纸质文档中的数字

## 1、整理《会计基础工作规范》关键点

1. 汉字大写金额的单位有 元、角、分 等
2. 汉字大写金额的关键字：零、壹、贰、叁、肆、伍、陆、柒、捌、玖、拾、佰、仟、万、亿等
3. 阿拉伯数字转汉字大写时，从右到左每 4 位分割一组
4. 大写金额数字到元或者角为止的，在 元 或者 角 字之后应当写 整 字，大写金额数字有分的，分字后面不写 整
5. 大写金额数字前未印有货币名称的，应当加填货币名称，即 "人民币"，货币名称与金额数字之间不得留有空白
6. 阿拉伯金额数字中间有 0 时，汉字大写金额要写 零 字
7. 阿拉伯数字金额中间连续有几个 0 时，汉字大写金额中可以只写一个 零 字

## 2、composer 安装

- ` $ composer require swordying/num2rmb `

### 代码示例

```php
// 引入类文件
require __DIR__.'/vendor/autoload.php';
## 声明配置项 默认 []
$config = [
    'start_mark' => '¥', ## 开始标志 默认：人民币
    'end_mark' => '正', ## 结束标志 默认：整
    'negative_mark' => '欠', ## 负数标志 默认：负
];
// 实例化
$numb2rmb = new \Swordying\num2rmb($config);

$number = 123456789.01;
$result = $numb2rmb -> handle($number);

echo $result;
```

## 备注
1. 支持负数，小数点后两位
2. 数字范围：0-100 万亿
