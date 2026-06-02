<?php
/**
 * 音乐管理系统 PHP version 5.4+
 *
 * @version     2.0
 * @author      战神~~巴蒂 <jyuucn@163.com>
 * @license     http://jyuu.cn/license [未经授权严禁私自出售，否者承担法律责任]
 * @copyright   2014 - 2017 JYmusic
 */

namespace app\services;

class Uploader 
{
    /**
     * 默认配置项
     * @var array
     */
    protected $options = [
        'limit' => null,
        'maxSize' => null,
        'extensions' => null,
        'mimeTypes' => null,
        'required' => false,
        'rootPath' => '',
        'saveDir'  => 'uploads/',
        'saveName' => ['auto', 10],
        'removeFiles' => true,
        'perms' => null,
        'replace' => true,
        'onCheck' => null,
        'onError' => null,
        'onSuccess' => null,
        'onUpload' => null,
        'onComplete' => null,
        'onRemove' => null
    ];
    
    /**
     * @var array|null
     */
    protected $field = null;
    
    /**
     * @var null
     */
    protected $downloadContent = null;
    
    /**
     * @var string
     */
    protected $fieldType = 'input';
    
    /**
     * File information
     * @var array
     */
    protected $objects = [];
    
    /**
     * File index
     * @var int
     */
    protected $index = 0;
    
    protected  $errorCodeMessages = [
        40020 => 'PHP 文件上传扩展没有开启',
        40021 => '上传的文件大小超过了 php.ini 中 upload_max_filesize 设置的值。',
        40022 => '上传的文件大小超过了 HTML 表单设置的大小。',
        40023 => '上传的文件部分丢失。',
        40024 => '上传文件丢失。',
        40026 => '缺少一个临时文件夹',
        40027 => '目标文件目录不存在或不可读，不能进行处理。',
        40028 => 'A PHP extension stopped the file upload',
        40029 => '文件上传选项在 php.ini 中被禁用',
        
        400201 => '上传文件大小超过了 php.ini 中 post_max_size 设置的值',
        400202 => '上传文件数量超过了 php.ini 中 max_file_uploads 设置的值',
        400203 => '没有找到上传文件所需要的字段',
        400204 => '没有选择文件，请选择一个',
        400206 => '上传文件后缀不允许！',
        400207 => '上传文件类型不允许！',
        400208 => '文件大小超过限制',
        400209 => '文件上传数量超过限制',
        400210 => '文件只有部分文件被上传！',
        400211 => '文件写入磁盘失败，请检查目录权限',
        400212 => '文件目录创建失败，请检查目录权限',
        400213 => '非法上传文件',
        400214 => '法图像文件！',
        400215 => '文件下载失败',
        400216 => '文件已存在',
    ];
    
    /**
     * @var array
     */
    protected $result = [
        "isComplete" => false,
        "files" => [],
        "fileErrors" => 0,
        "errorCode" => 0,
        "error" => null,
    ];
    
    /**
     * Uploader constructor.
     * @param mixed $field
     * @param array $options
     */
    public function __construct($field, $options = [])
    {
        // __construct function
        $this->setOptions($options);
        if ($this->isURL($field)) {
            $this->fieldType = 'link';
            $this->objects[] = $this->getRemoteInfo($field);
        } else {
            $this->initialize($field);
        }
    }
    
    /**
     * @param $field
     */
    protected function initialize($field)
    {
        if (ini_get('file_uploads') == false) {
            $this->setError(40029);
        }elseif (isset($_FILES[$field])) {
            $files = $_FILES[$field];
            if (is_array($files['tmp_name']) === true) {
                foreach ($files['tmp_name'] as $index => $tmpName) {
                    $this->objects[] = $this->getLocalInfo($files, $index);
                }
            } else {
                $this->objects[] = $this->getLocalInfo($files);
            }
        } else {
            $this->setError(400203);
        }
    }
    
    /**
     * 本地文件信息
     * @param array $files
     * @param int $index
     * @return array
     */
    protected function getLocalInfo ($files, $index = false)
    {
        if (false === $index ) {
            $tmpName = $files['tmp_name'];
            $size   = $files['size'];
            $name = $files['name'];
            $mimeType = $files['type'];
            $error = $files['error'];
        } else {
            $tmpName = $files['tmp_name'][$index];
            $size   = $files['size'][$index];
            $name = $files['name'][$index];
            $mimeType = $files['type'][$index];
            $error = $files['error'][$index];
        }
        
        $fileInfo = [
            "name" => $name,
            "tmp_name"=> $tmpName,
            "mime_type"=> $mimeType ,
            'extension' => pathinfo($name, PATHINFO_EXTENSION),
            "size"=> $size,
            "format_size"=> $this->formatSize($size),
            "md5"=> md5_file($tmpName),
            "sha1"=> hash_file('sha1', $tmpName),
            "error"=> $error
        ];
        
        return $fileInfo;
    }
    
    /**
     * 远程文件信息
     * @param string $source
     * @return array
     */
    protected function getRemoteInfo($source)
    {
        set_time_limit(80);
        $forInfo = [
            "size" => 1,
            "type" => "text/plain"
        ];
        
        $fileContent = file_get_contents($source);
        $headers = implode(" ", $http_response_header);
        if(preg_match('/Content-Length: (\d+)/', $headers, $matches)) {
            $forInfo['size'] = $matches[1];
        }
    
        if(preg_match('/Content-Type: (\w+\/\w+)/', $headers, $matches)) {
            $forInfo['type'] = $matches[1];
        }
        
        $this->downloadContent = $fileContent;
        
        return $forInfo;
    }

    /**
     * 执行上传
     *
     * @param array|string  $field
     * @param array|null  $options
     * @return array
     */
    public function upload()
    {
        if ($this->isValid()) {
            foreach ($this->objects as $index => $file) {
                $this->index = $index;
                if (false === $this->factory() || false ===  $this->saveFile()) {
                    ++$this->result['fileErrors'];
                }
            }
            $this->result['isComplete'] = true;
            $this->_onComplete($this->objects);
        }
        return $this->getResult();
    }
    
    /**
     * 单个文件上传
     * @param int $key
     */
    public function factory()
    {
        //自定义上传回调
        $call = $this->_onUpload($this->objects[$this->index]);
        if (is_array($call)) {
            $this->objects[$this->index] = array_merge($this->objects[$this->index], $call);
        }
        
        $file = $this->objects[$this->index];
        if (isset($file['is_have'])) {
            unset($this->objects[$this->index]['tmp_name']);
            $this->setError(400216, $this->index);
            return false;
        }
        
        if (false === $this->validate()) {
            $this->_onError($file);
            $this->objects[$this->index] = [
                'error' =>  $this->objects[$this->index]['error'],
                'errorCode' => $this->objects[$this->index]['errorCode']
            ];
            return false;
        }
        return true;
    }
    
    /**
     * 保存文件
     * @return bool
     */
    protected function saveFile()
    {
        //设置上传路径
        $this->objects[$this->index]['save_name'] = $this->createSaveName();
        
        $file = $this->objects[$this->index];
        $savFile =  $this->options['saveDir'] . $file['save_name'];
        
        if (@move_uploaded_file($file['tmp_name'], $this->options['rootPath'] . $savFile)) {
            unset($this->objects[$this->index]['tmp_name']);
            $this->objects[$this->index]['save_root'] = $this->options['rootPath'];
            $this->objects[$this->index]['save_path'] = str_replace("\\", "/", $savFile);
            $call = $this->_onSuccess($this->objects[$this->index]);
            if (is_array($call)) {
                $this->objects[$this->index] = array_merge($this->objects[$this->index], $call);
            } elseif (false === $call) {
                @unlink($this->options['rootPath'] . $savFile);
                $this->objects[$this->index] = [
                    'error' => $this->errorCodeMessages[400211],
                    'errorCode' => 400211
                ];
                return false;
            }
            return true;
        }
        
        $this->_onError($file);
        $this->setError(400211, $this->index);
        return false;
    }
    
    /**
     * 全局验证
     * @return bool
     */
    protected function isValid()
    {
        $fileCount = count($this->objects);
        if ($fileCount < 0) {
            $this->setError(400204);
            return false;
        }
        
        $maxUploads = intval(ini_get('max_file_uploads'));
        if ($maxUploads && $maxUploads < $fileCount) {
            $this->setError(400202);
            return false;
        }
        
        if($this->options['limit'] && ($fileCount > $this->options['limit'])) {
            $this->setError(400209);
            return false;
        }
        
        $uploadDir = $this->options['rootPath'] . $this->options['saveDir'];
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
            $this->setError(400212);
            return false;
        }
        
        if(!is_writable($uploadDir)) {
            @chmod($uploadDir, 750);
        }
        
        if($this->fieldType == "input") {
            $totalSize = 0;
            
            foreach($this->objects as $value) {
                $totalSize += $value['size'];
            }
            
            $totalSize = $totalSize/1048576;
            
            if($this->options['maxSize'] && $totalSize > $this->options['maxSize']) {
                $this->setError(400208);
                return false;
            }
            
            $maxSize = intval(ini_get('upload_max_filesize'));
            if ($maxSize && $totalSize > $maxSize) {
                $this->setError(40021);
                return false;
            }
            
            $maxSize = intval(ini_get('post_max_size'));
            if($maxSize && $totalSize > $maxSize) {
                $this->setError(400201);
                return false;
            }
        }
        return true;
    }
    
    /**
     * 验证文件
     *
     * @param int $key
     * @return bool
     */
    protected function validate()
    {
        $key = $this->index;
        $file = $this->objects[$key];
        $options = $this->options;
        
        //检测基础错误
        if($file['error'] > 0) {
            $this->setError(400200 + $file['error'], $key);
            return false;
        }
        
        //后缀检测
        $extension = strtolower($file['extension']);
        if($options['extensions'] && !in_array($extension, $options['extensions'])) {
            $this->setError(400206, $key);
            return false;
        }
        
        //文件类型检测
        $mimeType = $this->getMimeType($key);
        if ($options['mimeTypes'] && !in_array($mimeType, $options['mimeTypes'])) {
            $this->setError(400207, $key);
            return false;
        }
    
        //图像严格验证
        if (in_array($extension, ['gif', 'jpg', 'jpeg', 'bmp', 'png', 'swf']) && !in_array($this->getImageType($file['tmp_name']), [1, 2, 3, 4, 6, 13])) {
            $this->setError(400214, $key);
            return false;
        }
     
        if($options['maxSize'] && $file['size'] > $options['maxSize']) {
            $this->setError(400208, $key);
            return false;
        }

        if(!is_uploaded_file($file['tmp_name'])) {
            $this->setError(400213, $key);
            return false;
        }
    
        $call = $this->_onCheck($this->objects[$key]);
        if (is_array($call)) {
            $this->objects[$key]['errorCode'] = $call[0];
            $this->objects[$key]['error'] = $call[1];
            return false;
        }
        return true;
	}
    
    /**
     * 获取结果
     * @return array
     */
    public function getResult()
    {
        $this->result['files'] = $this->objects;
        return $this->result;
    }
    
	/**
     * removeFiles method
     *
     * Remove files or cancel upload for them
     * @return array
     */
	private function removeFiles()
	{
        $removed_files = [];
        if($this->options['removeFiles'] !== false){
            foreach($_POST as $key=>$value){
                preg_match((is_string($this->options['removeFiles']) ? $this->options['removeFiles'] : '/jfiler-items-exclude-'.$this->field['Field_Name'].'-(\d+)/'), $key, $matches);
                if(isset($matches) && !empty($matches)){
                    $input = $_POST[$matches[0]];
                    if($this->isJson($input)) {
                        $removed_files = json_decode($input, true);
                    }

                    $custom = $this->_onRemove($removed_files, $this->field);
                    if($custom && is_array($custom)) {
                        $removed_files = $custom;
                    }
                }
            }
        }

        return $removed_files;
	}

    /**
     * downloadFile method
     *
     * Download file to server
     * @return boolean
     */
    private function downloadFile($source, $destination, $info = false)
    {
        set_time_limit(80);

        $forInfo = [
            "size" => 1,
            "type" => "text/plain"
        ];

        if(!isset($this->cache_download_content)){
            $file_content = file_get_contents($source);
            if($info){
                $headers = implode(" ", $http_response_header);
                if(preg_match('/Content-Length: (\d+)/', $headers, $matches)) {
                    $forInfo['size'] = $matches[1];
                }
                
                if(preg_match('/Content-Type: (\w+\/\w+)/', $headers, $matches)) {
                    $forInfo['type'] = $matches[1];
                }
                
                $this->cache_download_content = $file_content;

                return $forInfo;
            }
        }else{
            $file_content = $this->cache_download_content;
        }

        $downloaded_file = @fopen($destination, 'w');
        $written = @fwrite($downloaded_file, $file_content);
        @fclose($downloaded_file);

        return $written;
    }

	/**
     * 创建文件名
     *
     * Generated a file name by uploading
     * @return boolean
     */
	private function createSaveName($nameRule = '', $replaceCheck = false)
    {
        $nameRule = empty($nameRule) ? $this->options['saveName'] : $nameRule;
        if (is_array($nameRule)) {
            $type = $nameRule[0];
        }elseif(is_null($nameRule)) {
            $type = 'unique';
        } else {
            $type = $nameRule;
        }
        
        $file = $this->objects[$this->index];
        $extension = !empty($file['extension']) ? "." . $file['extension'] : "";
        
        $saveName = "";
        $usedExtension = false;

		switch($type){
			case "auto":
                $length = isset($nameRule[1]) ? $nameRule[1] : 10;
                $saveName = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
			break;
			case "original":
                $saveName = $file['name'];
			break;
			default:
                $saveName = $type;
		}
        
        if(!$this->options['replace'] && !$replaceCheck){
            $i = 1;
            $checkName = $saveName;
            $checkFile = $this->options['rootPath'] . $this->options['saveDir']  . $checkName . (!$usedExtension ? $extension : '');
            
            while (file_exists($checkFile)) {
                $checkName = $this->createSaveName($saveName . "({$i})", true);
                $i++;
            }
            $saveName = $checkName;
        }
        
        if(!$usedExtension && !$replaceCheck) {
            $saveName .= $extension;
        }
        
		return $saveName;
	}
    
    /**
     * 获取文件类型
     * @param int $key
     * @return string
     */
    protected function getMimeType($key = 0)
    {
        $file = $this->objects[$key];
        //暂时获取上传的文件类型
        return $file['mime_type'];
    }
    
    // 获取图像类型
    protected function getImageType($image)
    {
        if (function_exists('exif_imagetype')) {
            return exif_imagetype($image);
        } else {
            $info = getimagesize($image);
            return $info[2];
        }
    }
    
    /**
     * 设置配置参数
     *
     * @param  array $options
     * @return bool
     */
    private function setOptions($options)
    {
        if(!empty($options) && is_array($options)) {
            $this->options = array_merge($this->options, $options);
        }
    }
    
	/**
     * 设置错误
     * @param $code
     */
	protected function setError($code, $key = null)
    {
        if (is_numeric($key)) {
            $this->objects[$key]['errorCode'] = $code;
            $this->objects[$key]['error'] = $this->errorCodeMessages[$code];
        } else {
            $this->result['errorCode'] = $code;
            $this->result['error'] = $this->errorCodeMessages[$code];
        }
    }
	
    /**
     * isJson method
     *
     * Check if string is a valid json
     * @return boolean
     */
    private function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * isURL method
     *
     * Check if string $url is a link
     * @return boolean
     */
    private function isURL($url)
    {
        return is_string($url) && preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
    }

    /**
     * formatSize method
     *
     * Convert file size
     * @return float
     */
    private function formatSize($bytes)
    {
        if ($bytes >= 1073741824){
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576){
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes > 0){
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }

	private function _onCheck($file = [])
    {
		if (is_callable($this->options['onCheck'])) {
		    return call_user_func($this->options['onCheck'], $file);
        }
		return  null;
	}
 
	private function _onSuccess($file = [], $metas = [])
    {
        if (is_callable($this->options['onSuccess'])) {
            return call_user_func($this->options['onSuccess'], $file, $metas);
        }
		return null;
	}

	private function _onError($errors =null, $file = [])
    {
        if (is_callable($this->options['onError'])) {
            return call_user_func($this->options['onError'], $errors, $file);
        }
        return null;
	}

	private function _onUpload($metas= [], $field = null)
    {
        if (is_callable($this->options['onUpload'])) {
            return call_user_func($this->options['onUpload'], $metas, $field);
        }
        return null;
    }

    private function _onComplete($files = [], $metas = [])
    {
        if (is_callable($this->options['onComplete'])) {
            return call_user_func($this->options['onComplete'], $files, $metas);
        }
        return null;
	}

    private function _onRemove($files = [], $field = null)
    {
        if (is_callable($this->options['onRemove'])) {
            return call_user_func($this->options['onRemove'], $files, $field);
        }
        return null;
    }
}
