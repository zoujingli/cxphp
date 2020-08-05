<?php

declare (strict_types=1);

// +----------------------------------------------------------------------
// | CxPHP 极速常驻内存框架 ~ 基于 WorkerMan 实现，极速及兼容并存
// +----------------------------------------------------------------------
// | 版权所有 2014~2020 广州楚才信息科技有限公司 [ http://www.cuci.cc ]
// +----------------------------------------------------------------------
// | 官方网站: http://www.cxphp.cn
// | 项目文档: http://doc.cxphp.cn
// +----------------------------------------------------------------------
// | 开源协议 ( https://mit-license.org )
// +----------------------------------------------------------------------
// | gitee 代码仓库：https://gitee.com/zoujingli/cxphp
// | github 代码仓库：https://github.com/zoujingli/cxphp
// +----------------------------------------------------------------------

namespace cxphp\http\request;

use cxphp\core\Exception;

/**
 * 上传文件处理
 * Class UploadFile
 * @package Webman\Http
 */
class UploadFile extends \SplFileInfo
{
    /**
     * @var string
     */
    protected $uploadName = null;

    /**
     * @var string
     */
    protected $uploadMimeType = null;

    /**
     * @var integer
     */
    protected $uploadErrorCode = null;

    /**
     * UploadFile constructor.
     * @param $filename
     * @param $uploadName
     * @param $uploadMimeType
     * @param $uploadErrorCode
     */
    public function __construct($filename, $uploadName, $uploadMimeType, $uploadErrorCode)
    {
        $this->uploadName = $uploadName;
        $this->uploadMimeType = $uploadMimeType;
        $this->uploadErrorCode = $uploadErrorCode;
        parent::__construct($filename);
    }

    /**
     * 文件移动操作
     * @param string $destination
     * @return $this
     * @throws Exception
     */
    public function move(string $destination)
    {
        set_error_handler(function ($type, $msg) use (&$error) {
            $error = $msg;
        });
        $path = pathinfo($destination, PATHINFO_DIRNAME);
        if (!is_dir($path) && !mkdir($path, 0755, true)) {
            restore_error_handler();
            throw new Exception(sprintf('Unable to create the "%s" directory (%s)', $path, strip_tags($error)));
        }
        if (!rename($this->getPathname(), $destination)) {
            restore_error_handler();
            throw new Exception(sprintf('Could not move the file "%s" to "%s" (%s)', $this->getPathname(), $destination, strip_tags($error)));
        }
        restore_error_handler();
        @chmod($destination, 0666 & ~umask());
        return new static($destination);
    }

    /**
     * @return string
     */
    public function getUploadName()
    {
        return $this->uploadName;
    }

    /**
     * @return string
     */
    public function getUploadMineType()
    {
        return $this->uploadMimeType;
    }

    /**
     * @return mixed
     */
    public function getUploadExtension()
    {
        return pathinfo($this->uploadName, PATHINFO_EXTENSION);
    }

    /**
     * @return integer
     */
    public function getUploadErrorCode()
    {
        return $this->uploadErrorCode;
    }

    /**
     * @return boolean
     */
    public function isValid()
    {
        return $this->uploadErrorCode === UPLOAD_ERR_OK;
    }

}