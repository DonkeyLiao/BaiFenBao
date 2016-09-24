<?php
// +----------------------------------------------------------------------
// | Describe: Dhb通用标签库解析类
// +----------------------------------------------------------------------
// | Author: dyhb <635750556@qq.com>
// +----------------------------------------------------------------------
// | Date: 2015-01-24
// +----------------------------------------------------------------------
namespace Think\Template\TagLib;
use Think\Template\TagLib;

/**
 * Dhb标签库解析类
 */
class Dhb extends TagLib {
	static $arrMinify = array();

//id':'area_id_field','field':'area_id','level':2,'value':nAreaID
    // 标签定义
    protected $tags   =  array(
        // 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
        'js_'       =>  array('attr'=>'file,version,local','close'=>0),
        'css_'      =>  array('attr'=>'file,version,id,local','close'=>0),
        'city'      =>  array('attr'=>'id,field,level,value,parent,class,required,position,placeholder,callback,width','close'=>0),
        'js_once'   =>  array(),
        'js_ignore' =>  array(),
        'js_pagination' =>  array(),
        'js_again'  =>  array('close'=>0),
        'script'    =>  array(),
        'style'     =>  array(),
		'minify'    =>  array('close'=>0,'nocache'=>0),  
        'dialog'    =>  array('attr'=>'id,title,width,height,formaction,formclass,formid,class,fok,fload,fcancel,fid,ftype,fclick,fextend,remark,noform'),
        'client'    =>  array('attr'=>'id,name,title,width,callback,field,defaultclienttype,defaultclientname,defaultclientid,shadow,required','close'=>0),
        'goods'    =>  array('attr'=>'id,name,title,width,callback,field,defaultgoodstype,defaultgoodsname,defaultgoodsid,shadow,required','close'=>0),
		'table'     =>  array('attr'=>'type,client,stock,edit','close'=>0),
        'staff'    =>  array('attr'=>'id,name,title,width,callback,field,defaultstaffname,defaultstaffid,defaulstafftype,shadow,required','close'=>0),
    );

    /**
     * js标签解析（方便以后合并JS）
     * @access public
     * @param array $tag 标签属性
     * @param string $content  标签内容
     * @todo 合并JS
     * @return string
     */
    public function _js_($tag,$content) {
        $file     =    $tag['file'];
        $version  =    isset($tag['v'])?$tag['v']:(DEVELOPMENT_MODE==='development'?rand(1000,50000):SET_VERSION);
		$nocache  =    isset($tag['nocache'])?$tag['nocache']:0;
        $local    =    isset($tag['local'])?$tag['local']:0;
                
        $file = array_filter(explode(',',$file));

		if(MINIFY_ON === false || $nocache == 1){
			$path = ($local == 1 ? __ROOT__.'/Public/static' : C('TMPL_PARSE_STRING.__STATIC__')).'/js/';
			$strReturnHtml = '';
			foreach($file as $sTemp){
				$strReturnHtml .= "<script src=\"{$path}{$sTemp}?_={$version}\"></script>\r\n";
			}
		}else{	
			$path = '//Public/static/js/';

			$arrName = array();
			foreach($file as $temp){
				$arrName[] = $path.$temp;
			}
			
			$strKey = md5(implode(',',$arrName)).'.js';
			
			// 生成网页调用Minify 地址
			$strOld = C('URL_MODEL');
			C('URL_MODEL','0');
			$strUrl = U("Quote/Min/index?_={$version}&g=".$strKey);
			$strUrl = str_replace('?s=','',$strUrl);
			C('URL_MODEL',$strOld);

			$strReturnHtml .= "<script src=\"{$strUrl}\"></script>\r\n";

			self::$arrMinify[$strKey] = $arrName;
		}
        
        return $strReturnHtml;
    }
    
    /**
     * css标签解析（方便以后合并CSS）
     * @access public
     * @param array $tag 标签属性
     * @param string $content  标签内容
     * @todo 合并CSS
     * @return string
     */
    public function _css_($tag,$content) {
        $file     =    $tag['file'];
        $id       =    isset($tag['id'])?$tag['id']:'';
        $version  =    isset($tag['v'])?$tag['v']:(DEVELOPMENT_MODE==='development'?rand(1000,50000):SET_VERSION);
		$nocache  =    isset($tag['nocache'])?$tag['nocache']:0;
        $local    =    isset($tag['local'])?$tag['local']:0;
                
        $file = array_filter(explode(',',$file));

		if(MINIFY_ON === false || $nocache == 1){
			$path = ($local == 1 ? __ROOT__.'/Public/static' : C('TMPL_PARSE_STRING.__STATIC__')).'/css/';

			$strReturnHtml = '';
			foreach($file as $sTemp){
				$strReturnHtml .= "<link type=\"text/css\"".($id ? ' id="'.$id.'"' : '' )." href=\"{$path}{$sTemp}?_={$version}\" rel=\"stylesheet\" />\r\n";
			}
		}else{
			$path = '//Public/static/css/';

			$arrName = array();
			foreach($file as $temp){
				$arrName[] = $path.$temp;
			}
			
			$strKey = md5(implode(',',$arrName)).'.css';
			
			// 生成网页调用Minify 地址
			$strOld = C('URL_MODEL');
			C('URL_MODEL','0');
			$strUrl = U("Quote/Min/index?_={$version}&g=".$strKey);
			$strUrl = str_replace('?s=','',$strUrl);
			C('URL_MODEL',$strOld);

			$strReturnHtml .= "<link type=\"text/css\" href=\"{$strUrl}\" rel=\"stylesheet\"/>\r\n";
			self::$arrMinify[$strKey] = $arrName;
		}
        
        return $strReturnHtml;
    }
    
    /**
     * 城市组件
     * @access public
     * @param array $tag 标签属性
     * @param string $content  标签内容
     * @return string
     */
    public function _city($tag,$content) {
        $id     =    isset($tag['id'])?trim($tag['id']):'';
        $field  =    isset($tag['field'])?$tag['field']:$id.'_area_id';
        $level  =    isset($tag['level'])?intval($tag['level']):3;
        $value  =    isset($tag['value'])?$tag['value']:'';
        $width  =    isset($tag['width'])?'width:'.$tag['width'].'px;':'';
        $parent =    isset($tag['parent'])?intval($tag['parent']):0;
        $class  =    isset($tag['class'])?trim($tag['class']):'';
        $required   =   isset($tag['required'])?'{required:true}':'';
        $position  =    isset($tag['position']) && in_array($tag['position'], array('top', 'bottom', 'auto'))?trim($tag['position']):'auto';
        $class = trim(strtolower(preg_replace("/([A-Z])/" , '-${1}',$class)),'-');
        $placeholder     =    isset($tag['placeholder'])?trim($tag['placeholder']):'';
        $callback     =    isset($tag['callback'])?trim($tag['callback']):'';

        if(empty($id)){
            return '';
        }
        
        $value = $this->parseAssignVar($value);
        
        $strReturnHtml=<<<EOT
<div style="position:relative;" onmouseleave="$(this).find('span').hide();" onmouseenter="$(this).find('span').show();" class="dhb-citybox-box">
    <script type="text/javascript">
    $(function(){
        $(_+'.dhb-citybox-box #dhbcity-{$id}')
            .val($.DHB.city_name({'area_id':'{$value}','parent':'{$parent}'}))
            .click(function(e){
                e.stopPropagation();
            });
    });
    </script>
    <input type="text" id="dhbcity-{$id}" class="citybox-input-name form-control {$class}" style="$width" readonly="true" onclick="$.DHB.city({'id':'dhbcity-{$id}','field':'{$field}','level':'{$level}','value':'{$value}','position':'{$position}','callback':'{$callback}'},this);" value="" placeholder="{$placeholder}" />
    <span style="z-index: 8; position: absolute; top: 1px; right: 1px; padding: 1px 1px 3px 3px; cursor: pointer; display: none; color:#d5d3d5;width:19px;height:27px;background:#fff;" onclick="$.DHB.clear_select_city(this,'{$callback}');"><i class="fa fa-times-circle"></i></span>
    <input type="hidden" name="{$field}" class="city-validate {$required}" value="{$value}" />
</div>
EOT;

        return $strReturnHtml;
    }
    
    /**
     * 禁止$.DHB._ 中方法多次执行
     * @access public
     * @param array $tag 标签属性
     * @param string $content  标签内容
     * @return string
     */
    public function _js_once($tag,$content) {
        $par = 'app.c.public_data[\''.CONTROLLER_NAME.'/'.ACTION_NAME.'\']';
        $ma = $par.'[\'once\']';
        $parseStr = $par.'='.$par.' || {};'."\r\n";
        $parseStr .= 'if('.$ma.'===false){'."\r\n";
        $parseStr .= $ma.' = true;'."\r\n";
        $parseStr .= $content."\r\n";
        $parseStr .='}';
        return $parseStr;
    }

    /**
     * 简化一下页面标签script
     * @access public
     * @param array $tag 标签属性
     * @param string $content  标签内容
     * @author 小牛New
     * @since 2015-07-16
     * @return string
     */
    public function _script($tag,$content) {
        $parseStr = '<script type="text/javascript">'."\r\n";
        $parseStr .= $content."\r\n";
        $parseStr .='</script>';
        return $parseStr;
    }
    
    /**
     * 简化一下页面标签css
     * @access public
     * @param array $tag 标签属性
     * @param string $content  标签内容
     * @author 小牛New
     * @since 2015-07-16
     * @return string
     */
    public function _style($tag,$content) {
    	$parseStr = '<style type="text/css">'."\r\n";
    	$parseStr .= $content."\r\n";
    	$parseStr .='</style>';
    	return $parseStr;
    }
    
    /**
     * 让 $.DHB._ 再执行一次
     * @access public
     * @param array $tag 标签属性
     * @param string $content  标签内容
     * @return string
     */
    public function _js_again($tag,$content) {
        $parseStr = 'app.c.public_data[\''.CONTROLLER_NAME.'/'.ACTION_NAME.'\'][\'once\']=false;'."\r\n";
        return $parseStr;
    }

    /**
     * 让 $.DHB._ 再执行一次，在执行过程中忽略一些代码[如果$.DHB_(true)则忽略本代码]
     * @access public
     * @param array $tag 标签属性
     * @param string $content  标签内容
     * @return string
     */
    public function _js_ignore($tag,$content) {
        $parseStr = 'if(typeof(arguments[0])===\'undefined\'){'."\r\n";
        $parseStr .= $content."\r\n";
        $parseStr .='}';
        return $parseStr;
    }

    /**
     * 让 $.DHB._ 再执行一次[如果$.DHB_(false)则再次执行本代码]
     * @access public
     * @param array $tag 标签属性
     * @param string $content  标签内容
     * @return string
     */
    public function _js_pagination($tag,$content) {
        $parseStr = 'if(typeof(arguments[0])===\'undefined\' || arguments[0]===false){'."\r\n";
        $parseStr .= $content."\r\n";
        $parseStr .='}';
        return $parseStr;
    }
	
	/**
     * 保存minify
     * @access public
     * @param array $tag 标签属性
     * @param string $content  标签内容
     * @return string
     */
    public function _minify($tag,$content) {
		if(MINIFY_ON === true){
			//fixme delete
			if(!is_file(RUNTIME_PATH.'/Static/group.php')){
				if(!is_dir(RUNTIME_PATH . 'Static') && function_exists('make_dir')) {
					make_dir(RUNTIME_PATH . 'Static');
				}

				if(!file_put_contents(RUNTIME_PATH.'/Static/group.php',
				"<?php\n /* DHB minify file,Do not to modify this! */ \n return ".
				var_export(self::$arrMinify, true).
				"\n?>")
				){
					E(RUNTIME_PATH.'/Static'.'无法没有可写权限!');
				}
			}
		}
    }
    
    /**
     * Dialog 模板框
     * @access public
     * @param array $tag 标签属性
     * @param string $content  标签内容
     * @author 小牛New
     * @since 2016-01-26
     * @return string
     */
    public $arrDialogData  = array();
    public function _dialog($tag,$content) {
        $arrData = array();
        $arrData['id']   =   isset($tag['id'])?'id="'.str_replace('~','-',$this->parseAssignVar(trim($tag['id']))).'"':'';// 对话框id
        $arrData['title']   =   isset($tag['title'])?str_replace('~','-',trim($tag['title'])):'';// 标题
        $arrData['width']   =   isset($tag['width'])?"width:".trim($tag['width'])."px;":'';// 宽度
        $arrData['height']   =   isset($tag['height'])?"height:".trim($tag['height'])."px;":'';// 高度
        $arrData['style']   =   $arrData['width'] || $arrData['height']?'style="'.$arrData['width'].$arrData['height'].'"':'';
        $arrData['content']   = $content;
        $arrData['class']   =   isset($tag['class'])?str_replace('~','-',trim($tag['class'])):'';// dialog class = class
        $arrData['formaction']   =   isset($tag['formaction'])?"action=\"".$this->parseAssignVar(trim($tag['formaction']))."\"":'';// form action
        $arrData['formclass']   =   isset($tag['formclass'])?str_replace('~','-',trim($tag['formclass'])):'validate';// form class = fclass
        $arrData['formid']   =   isset($tag['formid'])?str_replace('~','-',trim($tag['formid'])):'';// form id = fid
        $arrData['modal_ok']   =   isset($tag['fok'])?trim($tag['fok']):'确定';// 确定按钮
        $arrData['modal_load']   =   isset($tag['fload'])?trim($tag['fload']).'...':'提交中...';// 提交中
        $arrData['modal_cancel']   =   isset($tag['fcancel'])?trim($tag['fcancel']):'取消';// 取消按钮
        $arrData['modal_id']   =   isset($tag['fid'])?str_replace('~','-',trim($tag['fid'])):'submit-button';// 默认提交按钮
        $arrData['modal_type']   =   isset($tag['ftype'])?trim($tag['ftype']):'submit';// 提交表单类型
        $arrData['modal_click']   =   isset($tag['fclick'])?"onclick=\"".str_replace('~','-',trim($tag['fclick']))."\"":'';// 点击事件
        $arrData['modal_extend']   =   isset($tag['fextend'])?str_replace('~','-',trim($tag['fextend'])):'';// 提交按钮扩展标签
        $arrData['remark_header']   =   isset($tag['remark'])?'<!-- start '.$tag['remark'].'-->':'';// 注释
        $arrData['remark_footer']   =   isset($tag['remark'])?'<!-- end '.$tag['remark'].' -->':'';
        $arrData['body_class'] = isset($_GET['nobodyclass']) ? '' : 'modal-body';
        $arrData['noform'] = isset($tag['noform']) && $tag['noform'] == '1'?true:false;
        
        $this->arrDialogData = $arrData;
        
        $parseStr = '
            {remark_header}
            <div class="modal fade in" {id} role="dialog">
                <div class="modal-dialog" {style}>
                    <div class="modal-content">
                        <div class="modal-header">
                            <button data-dismiss="modal" class="close" type="button">×</button>
                            <h4 class="modal-title">{title}</h4>
                        </div>
                        '.($arrData['noform'] ? '' : '<form class="form-horizontal {formclass} f0" method="post" id="{formid}" {formaction}>').'
                            <div class="{body_class} tab-content {class}">
                                {content}
                            </div>
                            <div class="modal-footer">
                                <button type="{modal_type}" id="{modal_id}" data-loading-text="{modal_load}" {modal_click} class="btn btn-info w-xs" {modal_extend}>{modal_ok}</button>
                                <button type="button" class="btn btn-default w-xs" data-dismiss="modal">{modal_cancel}</button>
                            </div>
                       '.($arrData['noform'] ? '' : '</form>').'
                   </div>
                </div>
            </div>
            {remark_footer}';

        $parseStr = preg_replace_callback(
                "/\{([0-9a-zA-Z\_\-\.\/]+)\}/",
                function($arrMatches){ 
                    return isset($this->arrDialogData[$arrMatches[1]]) ? $this->arrDialogData[$arrMatches[1]] : $arrMatches[1]; 
                },$parseStr
        );
        
        return $parseStr;
    }

    /**
     * 客户ID
     * @access public
     * @param array $tag 标签属性
     * @param string $content  标签内容
     * @author 小牛New
     * @since 2016-01-27
     * @return string
     */
    public function _client($tag,$content) {
        $arrData = array();
        $arrData['client_id']   =   isset($tag['id'])?str_replace('~','-',trim($tag['id'])):'client_id';// 接收返回的ID
        $arrData['client_name']   =   isset($tag['name'])?str_replace('~','-',trim($tag['name'])):'client_name';// 接收返回值的名字
        $arrData['dialog_title']   =   isset($tag['title'])?$tag['title']:'';// 对话框标题
        $arrData['style']   =   isset($tag['style'])?$tag['style']:(isset($tag['width'])?'width:'.$tag['width'].'px;':'');// 
        $arrData['callback']   =   isset($tag['callback'])?$tag['callback']:'';
        $arrData['field']   =   isset($tag['field'])?$tag['field']:'客户';
        $arrData['shadow']   =   isset($tag['shadow'])?$tag['shadow']:'btn-none';
        $arrData['defaultclienttype'] =   isset($tag['defaultclienttype'])?$tag['defaultclienttype']:'';
        $arrData['defaultclientname'] =   isset($tag['defaultclientname'])?$tag['defaultclientname']:'';
        $arrData['defaultclientid']   =   isset($tag['defaultclientid'])?$tag['defaultclientid']:'';
        $arrData['required']   =   isset($tag['required'])?'{required:true}':'';

		$arrData['defaultclientname'] = $this->parseAssignVar($arrData['defaultclientname']);
		$arrData['defaultclientid'] = $this->parseAssignVar($arrData['defaultclientid']);
        
        $strHtml = 
           '<div class="input-group" style="'.$arrData['style'].'position:relative;" onmouseenter="$(this).find(\'span:last()\').show();" onmouseleave="$(this).find(\'span:last()\').hide();" >
                <span class="input-group-btn">
                    <button class="btn btn-default btn-sm f-h-30 m-l-none '.$arrData['shadow'].'" type="button" style="border-right:none;" onclick="$.DHB.client.select_client({\'title\':\''.$arrData['dialog_title'].'\',\'client_name\':\''.$arrData['client_name'].'\',\'client_id\':\''.$arrData['client_id'].'\',\'client_callback\':\''.$arrData['callback'].'\',\'client_type\':\''.$arrData['defaultclienttype'].'\'});"><i class="icon-users"></i></button>
                </span>
                <input placeholder="'.($arrData['dialog_title'] ?: "名称/编码（回车）").'" type="text" value="'.$arrData['defaultclientname'].'" autocomplete="off" id="'.$arrData['client_name'].'" class="form-control '.$arrData['shadow'].' '.$arrData['required'].'" data-toggle="dropdown" data-dialog_title="'.$arrData['dialog_title'].'" data-name="'.$arrData['client_name'].'" data-id="'.$arrData['client_id'].'" data-callback="'.$arrData['callback'].'" data-init="0" data-initenter="0" data-initkeydown="0" data-client_type="'.$arrData['defaultclienttype'].'" onclick="$.DHB.client.client(this);" ondblclick=" $(this).parents(\'.input-group\').removeClass(\'open\');$.DHB.client.select_client({\'title\':\''.$arrData['dialog_title'].'\',\'client_name\':\''.$arrData['client_name'].'\',\'client_id\':\''.$arrData['client_id'].'\',\'client_callback\':\''.$arrData['callback'].'\',\'client_type\':\''.$arrData['defaultclienttype'].'\'});">
                <input type="hidden" value="'.$arrData['defaultclientid'].'" name="'.$arrData['client_id'].'" id="'.$arrData['client_id'].'">
                <div class="dropdown-menu animated fadeInUp" style="position:absolute;width:100%;">
                    <div class="panel bg-white">
                        <div style="overflow-y:auto;height:221px;" class="list-group">
                        </div>
                    </div>
                </div>
                <span data-name="'.$arrData['client_name'].'" data-id="'.$arrData['client_id'].'" data-callback="'.$arrData['callback'].'" onclick="$.DHB.client.clear_select_client(this);" style="display:none;z-index:8;position:absolute;top:5px;right:1px;padding:1px 1px 1px 3px;cursor:pointer;color:#d5d3d5;width:18px;height:22px;background:#fff;"><i class="fa fa-times-circle"></i></span>
           </div>';

        return $strHtml;
    }

    /**
     * 商品ID
     * @access public
     * @param array $tag 标签属性
     * @param string $content  标签内容
     * @author 小牛New
     * @since 2016-03-22
     * @return string
     */
    public function _goods($tag,$content) {
        $arrData = array();
        $arrData['goods_id']   =   isset($tag['id'])?str_replace('~','-',trim($tag['id'])):'goods_id';// 接收返回的ID
        $arrData['goods_name']   =   isset($tag['name'])?str_replace('~','-',trim($tag['name'])):'goods_name';// 接收返回值的名字
        $arrData['dialog_title']   =   isset($tag['title'])?$tag['title']:'';// 对话框标题
        $arrData['style']   =   isset($tag['style'])?$tag['style']:(isset($tag['width'])?'width:'.$tag['width'].'px;':'');// 
        $arrData['callback']   =   isset($tag['callback'])?$tag['callback']:'';
        $arrData['field']   =   isset($tag['field'])?$tag['field']:'';
        $arrData['shadow']   =   isset($tag['shadow'])?$tag['shadow']:'btn-none';
        $arrData['defaultgoodstype'] =   isset($tag['defaultgoodstype'])?$tag['defaultgoodstype']:'';
        $arrData['defaultgoodsname'] =   isset($tag['defaultgoodsname'])?$tag['defaultgoodsname']:'';
        $arrData['defaultgoodsid']   =   isset($tag['defaultgoodsid'])?$tag['defaultgoodsid']:'';
        $arrData['required']   =   isset($tag['required'])?'{required:true}':'';

		$arrData['defaultgoodsname'] = $this->parseAssignVar($arrData['defaultgoodsname']);
		$arrData['defaultgoodsid'] = $this->parseAssignVar($arrData['defaultgoodsid']);
        
        $strHtml = 
           '<div class="input-group" style="'.$arrData['style'].'position:relative;" onmouseenter="$(this).find(\'span:last()\').show();" onmouseleave="$(this).find(\'span:last()\').hide();" >
                <span class="input-group-btn">
                    <button class="btn btn-default btn-sm f-h-30 m-l-none '.$arrData['shadow'].'" type="button" style="border-right:none;" onclick="$.DHB.table.select_goods(this,{\'title\':\''.$arrData['dialog_title'].'\',\'goods_name\':\''.$arrData['goods_name'].'\',\'goods_id\':\''.$arrData['goods_id'].'\',\'goods_callback\':\''.$arrData['callback'].'\',\'goods_type\':\''.$arrData['defaultgoodstype'].'\',\'type\':\'1\'});"><i class="icon-bag"></i></button>
                </span>
                <input placeholder="'.($arrData['dialog_title'] ?: "名称/拼音/编号/关键字/条形码（回车键搜索）").'" placeholder="'.$arrData['dialog_title'].'" type="text" value="'.$arrData['defaultgoodsname'].'" autocomplete="off" id="'.$arrData['goods_name'].'" class="form-control '.$arrData['shadow'].' '.$arrData['required'].'" data-toggle="dropdown" data-dialog_title="'.$arrData['dialog_title'].'" data-name="'.$arrData['goods_name'].'" data-id="'.$arrData['goods_id'].'" data-callback="'.$arrData['callback'].'" data-init="0" data-initenter="0" data-initkeydown="0" data-goods_type="'.$arrData['defaultgoodstype'].'" onclick="$.DHB.table.goods_orders(this);" ondblclick="$.DHB.table.select_goods(this,{\'title\':\''.$arrData['dialog_title'].'\',\'goods_name\':\''.$arrData['goods_name'].'\',\'goods_id\':\''.$arrData['goods_id'].'\',\'goods_callback\':\''.$arrData['callback'].'\',\'goods_type\':\''.$arrData['defaultgoodstype'].'\',\'type\':\'1\'});">
                <input type="hidden" value="'.$arrData['defaultgoodsid'].'" name="'.$arrData['goods_id'].'" id="'.$arrData['goods_id'].'">
                <div class="dropdown-menu animated fadeInUp" style="position:absolute;width:100%;">
                    <div class="panel bg-white">
                        <div style="overflow-y:auto;height:221px;" class="list-group">
                        </div>
                    </div>
                </div>
                <span data-name="'.$arrData['goods_name'].'" data-id="'.$arrData['goods_id'].'" data-callback="'.$arrData['callback'].'" onclick="$.DHB.table.clear_select_goods(this);" style="display:none;z-index:8;position:absolute;top:1px;right:1px;padding:5px 1px 3px 5px;cursor:pointer;color:#d5d3d5;width:20px;height:25px;background:#fff;"><i class="fa fa-times-circle"></i></span>
           </div>';

        return $strHtml;
    }

    /**
     * 员工ID
     * @access public
     * @param array $tag 标签属性
     * @param string $content  标签内容
     * @author 小牛New
     * @since 2016-02-24
     * @return string
     */
    public function _staff($tag,$content) {
        $arrData = array();
        $arrData['staff_id']   =   isset($tag['id'])?str_replace('~','-',trim($tag['id'])):'staff_id';// 接收返回的ID
        $arrData['staff_name']   =   isset($tag['name'])?str_replace('~','-',trim($tag['name'])):'staff_name';// 接收返回值的名字
        $arrData['dialog_title']   =   isset($tag['title'])?$tag['title']:'';// 对话框标题
        $arrData['style']   =   isset($tag['style'])?$tag['style']:(isset($tag['width'])?'width:'.$tag['width'].'px;':'');// 
        $arrData['callback']   =   isset($tag['callback'])?$tag['callback']:'';
        $arrData['field']   =   isset($tag['field'])?$tag['field']:'员工';
        $arrData['shadow']   =   isset($tag['shadow'])?$tag['shadow']:'btn-none';
        $arrData['defaultstaffname'] =   isset($tag['defaultstaffname'])?$this->parseAssignVar(trim($tag['defaultstaffname'])):'';
        $arrData['defaultstaffid']   =   isset($tag['defaultstaffid'])?$this->parseAssignVar(trim($tag['defaultstaffid'])):'';
        $arrData['defaulstafftype'] =   isset($tag['defaulstafftype'])?$tag['defaulstafftype']:'';
        $arrData['required']   =   isset($tag['required'])?'{required:true}':'';

        $strHtml = 
           '<div class="input-group" style="'.$arrData['style'].'position:relative;" onmouseenter="$(this).find(\'span:last()\').show();" onmouseleave="$(this).find(\'span:last()\').hide();" >
                <span class="input-group-btn">
                    <button class="btn btn-default btn-sm f-h-30 m-l-none '.$arrData['shadow'].'" type="button" style="border-right:none;" onclick="$.DHB.staff.select_staff({\'title\':\''.$arrData['dialog_title'].'\',\'staff_name\':\''.$arrData['staff_name'].'\',\'staff_id\':\''.$arrData['staff_id'].'\',\'staff_type\':\''.$arrData['defaulstafftype'].'\',\'staff_callback\':\''.$arrData['callback'].'\',\'type\':\'one\'});"><i class=" icon-user"></i></button>
                </span>
                <input title="'.$arrData['field'].'" placeholder="'.($arrData['dialog_title'] ?: "名称/编码（回车）").'" type="text" value="'.$arrData['defaultstaffname'].'" autocomplete="off" id="'.$arrData['staff_name'].'" class="form-control '.$arrData['shadow'].' '.$arrData['required'].'" data-toggle="dropdown" data-dialog_title="'.$arrData['dialog_title'].'" data-name="'.$arrData['staff_name'].'" data-id="'.$arrData['staff_id'].'" data-callback="'.$arrData['callback'].'" data-stafftype="'.$arrData['defaulstafftype'].'" data-type="one" data-init="0" data-initenter="0" data-initkeydown="0" onclick="$.DHB.staff.staff(this);" ondblclick=" $(this).parents(\'.input-group\').removeClass(\'open\');$.DHB.staff.select_staff({\'title\':\''.$arrData['dialog_title'].'\',\'staff_name\':\''.$arrData['staff_name'].'\',\'staff_id\':\''.$arrData['staff_id'].'\',\'staff_type\':\''.$arrData['defaulstafftype'].'\',\'staff_callback\':\''.$arrData['callback'].'\',\'type\':\'one\'});">
                <input type="hidden" value="'.$arrData['defaultstaffid'].'" name="'.$arrData['staff_id'].'" id="'.$arrData['staff_id'].'">
                <div class="dropdown-menu animated fadeInUp" style="position:absolute;width:100%;">
                    <div class="panel bg-white">
                        <div style="overflow-y:auto;height:221px;" class="list-group">
                        </div>
                    </div>
                </div>
                <span data-name="'.$arrData['staff_name'].'" data-id="'.$arrData['staff_id'].'" data-callback="'.$arrData['callback'].'" onclick="$.DHB.staff.clear_select_staff(this);" style="display:none;z-index:8;position:absolute;top:1px;right:1px;padding:5px 2px 5px 5px;cursor:pointer;color:#d5d3d5;width:20px;height:21px;background:#fff;"><i class="fa fa-times-circle"></i></span>
           </div>';

        return $strHtml;
    }

	/**
     * 订单选择商品标签库
     * @access public
     * @param array $tag 标签属性
     * @param string $content  标签内容
     * @author 小牛New
     * @since 2016-01-28
     * @return string
     */
    public function _table($tag,$content) {
		$arrData = array();
        $arrData['client']   =   isset($tag['client'])?str_replace('~','-',trim($tag['client'])):'';// orders 订单先选客户后选商品
        $arrData['stock']   =   isset($tag['stock'])?str_replace('~','-',trim($tag['stock'])):'';// warehousing 入库单先选仓库后选商品
        $arrData['type']   =   isset($tag['type'])?str_replace('~','-',trim($tag['type'])):'';//
        $arrData['edit']   =   isset($tag['edit'])?true:false;// 是否为编辑状态

        $GLOBALS['_table_edit_'] = $arrData['edit'];
        $arrTable = (array)include(APP_PATH.'Common/Tpl/table.tpl');
        unset($GLOBALS['_table_edit_']);
        if(!isset($arrTable[$arrData['type']])) {
            return '请设置正确的类型（Type）!';
        }
        $arrOption = $arrTable[$arrData['type']];

		$strHeight = 'height:58px;';
		$strBorder = 'border:none;';
		$strHtml = '<div class="dhb-table"><table data-type="'.$arrData['type'].'" '.($arrData['client'] ? ' data-client="'.$arrData['client'].'" ' : '').($arrData['stock'] ? ' data-stock="'.$arrData['stock'].'" ' : '').'data-init="0" data-enterinit="0" onmouseenter="$.DHB.table.init(this);" class="table m-b-none table-bordered status-box" style="table-layout: fixed">';
			$strHtml .= '<thead>';
			foreach($arrOption['field'] as $strKey=>$arrVal){
				$strHtml .= "<th width=\"{$arrVal['width']}\" ".($arrVal['align'] ? 'class="text-'.$arrVal['align'].'"' : '').">{$arrVal['title']}</th>";
			}
			$strHtml .= '</thead>';

			for($i=1;$i<=11;$i++){
				$strHtml .= "<tr data-index=\"".($i-1)."\" data-item=\"\" ".($i==11 ? 'style="display:none;" hide="1"' : '').">";
				foreach($arrOption['field'] as $strKey=>$arrVal){
					$strField = '';
					switch($strKey){
						case 'index':
							$strField .= $i;
							break;
						case 'operate':
							$strField .= '<div><a onclick="$.DHB.table.add(this);"><i class="fa fa-plus fa-fw add-button"></i></a><a onclick="$.DHB.table.del(this);"><i class="fa fa-minus fa-fw delete-button"></i></a></div>';
							break;
						case 'name':
							$strField .= '
								<div class="goods1" style="display:block;width: 100%; height: 58px;"></div>
								<div class="goods2" style="display:none;text-align:left;">
									<div class="goods-img" style="width: 50px; height: 50px; margin-right: 5px; float:left;display: inline-block;"><img src="'.__PUBLIC__.'/static/images/default.png" style="width:100%; height:100%;" ></div>
									<div class="goods-num" style="display: block; height: 25px; line-height: 30px; color: rgb(153, 153, 153);"></div>
								    <div class="goods-name cut-out niceTitle" title="" style="width: auto;"></div>
								</div>
								<div class="goods3" style="display:none;padding:0;">
									<div class="input-group" style="'.$strHeight.'">
										<input type="text" name="goods-post[]" data-toggle="dropdown"  style="width:100%;'.$strHeight.$strBorder.'" onclick="$.DHB.table.goods_dropmenu(this);" data-initenter="0" data-initkeydown="0" data-conversion="" autocomplete="off" placeholder="名称/拼音/编号/关键字/条形码（回车键搜索）" />
										<span class="input-group-btn">
											<button class="btn btn-default btn-lg" type="button" style="'.$strHeight.$strBorder.'margin-left:1px;" onclick="$.DHB.table.select_goods(this);$(this).parents(\'.input-group\').removeClass(\'open\');">
												...
											</button>
										</span>
										<div class="dropdown-menu animated fadeInUp" style="width:600px;z-index:999999">
											<div class="panel bg-white">
												<div class="list-group" style="overflow-y:auto;height:235px;"></div>
												</div>
											</div>
										</div>
								</div>
								<div class="goods4" style="display:none;"></div>';
							break;
						case 'remark':
							$strField = '<p class="cut-out" style="display:block;">&nbsp;</p><div title="" class="look-out1" style="right:55px;"><div class="second-height-operate"><a onclick="$.DHB.table.remark(this);" class="bg-state bg-state-info">添加</a></div></div>';
							break;
						default:
                            $strTd = isset($arrVal['tdclass']) ? ' class="'.$arrVal['tdclass'].'" ' : '';
							$strField = '<div'.$strTd.'></div>';
							break;
					}

					$strHtml .= "<td data-init=\"0\" ".($arrVal['click'] ? 'onclick="'.$arrVal['click'].'"' : '')." field=\"{$strKey}\" class=\"{$arrVal['class']} ".($arrVal['align'] ? 'text-'.$arrVal['align'].' ' : '').($arrVal['edit']=='1' ? 'table-edit ' : '')."\">{$strField}</td>";
				}
				$strHtml .= '</tr>';
			}
			
		$strHtml .= '</table>
			<div class="cache-init-goods"></div>
			<input type="hidden" name="table_result" value="" />
		</div><script type="text/javascript">$(function(){
            setTimeout(function(){$(_+".dhb-table .list-operate .look-out1").hide();},20);
        });</script>';
		return $strHtml;
	}

}
