<?php declare(strict_types=1);

namespace Toolkit\Stdlib\Str\Traits;

use function count;
use function file_get_contents;
use function is_string;
use function preg_replace;
use function str_replace;
use function substr;
use function trim;

/**
 * trait StringOtherHelperTrait
 */
trait StringOtherHelperTrait
{

    ////////////////////////////////////////////////////////////////////////
    /// Format
    ////////////////////////////////////////////////////////////////////////

    /**
     * format description
     *
     * @param       $str
     * @param array $replaceParams 用于 str_replace('search','replace',$str )
     * @param array $pregParams    用于 preg_replace('pattern','replace',$str)
     *
     * @return string [type]                [description]
     * @example
     *        $pregParams = [
     *        'xx',  //'pattern'
     *        'yy',  //'replace'
     *        ]
     *        $pregParams = [
     *        ['xx','xx2'],  //'pattern'
     *        ['yy','yy2'],  //'replace'
     *        ]
     * @example
     *  $replaceParams = [
     *        'xx',  //'search'
     *        'yy', //'replace'
     *   ]
     *  $replaceParams = [
     *        ['xx','xx2'],  //'search'
     *        ['yy','yy2'],  //'replace'
     *  ]
     */
    public static function format($str, array $replaceParams = [], array $pregParams = []): string
    {
        if (!is_string($str) || !$str || (!$replaceParams && !$pregParams)) {
            return $str;
        }

        if ($replaceParams && count($replaceParams) === 2) {
            [$search, $replace] = $replaceParams;
            $str = str_replace($search, $replace, $str);
        }

        if ($pregParams && count($pregParams) === 2) {
            [$pattern, $replace] = $pregParams;
            $str = preg_replace($pattern, $replace, $str);
        }

        return trim($str);
    }

    /**
     * 格式化，用空格分隔各个词组
     *
     * @param string $keyword 字符串
     *
     * @return string 格式化后的字符串
     */
    public static function wordFormat(string $keyword): string
    {
        // 将全角角逗号换为空格
        $keyword = str_replace(['，', ','], ' ', $keyword);

        return preg_replace([
            // 去掉两个空格以上的
            '/\s(?=\s)/',
            // 将非空格替换为一个空格
            '/[\n\r\t]/'
        ], ['', ' '], trim($keyword));
    }

    /**
     * 缩进格式化内容，去空白/注释
     *
     * @param string $fileName
     * @param int $type
     *
     * @return mixed
     */
    public static function deleteStripSpace($fileName, $type = 0)
    {
        $data = trim(file_get_contents($fileName));
        $data = str_starts_with($data, '<?php') ? substr($data, 5) : $data;
        $data = str_ends_with($data, '?>') ? substr($data, 0, -2) : $data;

        //去掉所有注释 换行空白保留
        if ((int)$type === 1) {
            $preg_arr = [
                '/\/\*.*?\*\/\s*/is',  // 去掉所有多行注释/* .... */
                '/\/\/.*?[\r\n]/is',   // 去掉所有单行注释//....
                '/\#.*?[\r\n]/is'      // 去掉所有单行注释 #....
            ];

            return preg_replace($preg_arr, '', $data);
        }

        $preg_arr = [
            '/\/\*.*?\*\/\s*/is', // 去掉所有多行注释 /* .... */
            '/\/\/.*?[\r\n]/is',  // 去掉所有单行注释 //....
            '/\#.*?[\r\n]/is',    // 去掉所有单行注释 #....
            '/(?!\w)\s*?(?!\w)/is' //去掉空白行
        ];

        return preg_replace($preg_arr, '', $data);
    }

}
