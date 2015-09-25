<?php
class Common_Utils{
    /**
     * 数组编码转换
     *
     * @param string $in  入编码
     * @param string $out 出编码 
     * @param array $res  数组&字符串
     * @return array
     */
	function iconv_array($in='gbk', $out='utf8', &$res){
		if (is_array($res)){
			foreach ($res as &$v) {
				if (is_array($v)){
					self::iconv_array($in, $out, $v);
				}else{
					$v = iconv($in, $out, $v);
				}
			}
		}else{
			$res = iconv($in, $out, $res);
		}
		return $res;
	}
    
}