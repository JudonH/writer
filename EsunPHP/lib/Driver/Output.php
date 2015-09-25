<?php
class Driver_Output{
    
    private static function _xml_dom($dom, $data, $tag, $row_tag){
        $rdom = $dom->createElement($tag);
        foreach ($data as $k=>$v) {
            $tag = is_int($k) ? $row_tag : $k;
            if(!is_array($v)){
                if(is_int($k)){
                    $xdom = $dom->createElement($tag);
                    $xdom->appendChild($dom->createTextNode($v));
                    $rdom->appendChild($xdom);
                }else{
                    $rdom->setAttribute($k, $v);
                }
            }else{
                $xdom = self::_xml_dom($dom, $v, $tag, $row_tag);
                $rdom->appendChild($xdom);
            }
	    }
        return $rdom;
    }

    /**
     * 转换数组为xml
     * @param array $data 目标数据
     * @param string $root_tag 根tag
     * @param string $row_tag 缺省tag
     * @param string $encoding 编码
     */
	public static function xml($data, $root_tag='data', $row_tag='row', $encoding='utf8'){
		$dom = new DOMDocument("1.0", $encoding);
	    $rdom = self::_xml_dom($dom, $data, $root_tag, $row_tag);
		$dom->appendChild($rdom);
		return $dom->saveXML();
	}
	
	/**
	 * 输出json格式
	 * @param mixed $data 目标数据
	 * @param int $option 打包选项
	 */
	public static function json($data, $option=0){
		return json_encode($val, $option);
	}
}

