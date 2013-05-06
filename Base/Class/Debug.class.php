<?php

class Debug {

    static $includefile = array();
    static $info = array();
    static $sqls = array();
    static $msg = array(
        E_WARNING => '警告',
        E_NOTICE => '提醒',
        E_STRICT => '警告',
        E_USER_ERROR => '错误',
        E_USER_WARNING => '警告',
        E_USER_NOTICE => '提醒',
        'Unkown ' => '未知'
    );

    //返回同一脚本中两次获取时间的差值
    static function queryTime() {
        return round((microtime(true) - REQUEST_TIME_START), 8);  //计算后以4舍5入保留8位返回
    }

    /* 错误 handler */

    static function Catcher($errno, $errstr, $errfile, $errline) {
        if (!isset(self::$msg[$errno]))
            $errno = 'Unkown';

        if ($errno == E_NOTICE || $errno == E_USER_NOTICE)
            $color = "#000088";
        else
            $color = "red";

        $mess = '<font color=' . $color . '>';
        $mess.='<b>' . self::$msg[$errno] . "</b>[在文件 {$errfile} 中,第 $errline 行]:";
        $mess.=$errstr;
        $mess.='</font>';
        self::addMsg($mess);
    }

    //添加调试消息
    static function addmsg($msg, $type = 0) {
        if (C("DEBUG") && C("DEBUG") == 1) {
            switch ($type) {
                case 0:
                    self::$info[] = $msg;
                    break;
                case 1:
                    self::$includefile[] = $msg;
                    break;
                case 2:
                    self::$sqls[] = $msg;
                    break;
            }
        }
    }

    //输出调试消息
    static function message() {
        echo '<div style="float:left;clear:both;text-align:left;font-size:11px;color:#888;width:95%;margin:10px;padding:10px;background:#fee;border:1px dotted #778855;z-index:100">';
        echo '<div style="float:left;width:100%;"><span style="float:left;width:200px;"><b>运行信息</b>( <font color="red">' . self::queryTime() . ' </font>秒):</span><span onclick="this.parentNode.parentNode.style.display=\'none\'" style="cursor:pointer;float:right;width:35px;background:#500;border:1px solid #555;color:white">关闭X</span></div><br>';
        echo '<ul style="margin:0px;padding:0 10px 0 10px;list-style:none">';
        if (count(self::$includefile) > 0) {
            echo '【自动包含】';
            foreach (self::$includefile as $file) {
                echo '<li>&nbsp;&nbsp;&nbsp;&nbsp;' . $file . '</li>';
            }
        }
        if (count(self::$info) > 0) {
            echo '<br>【系统信息】';
            foreach (self::$info as $info) {
                echo '<li>&nbsp;&nbsp;&nbsp;&nbsp;' . $info . '</li>';
            }
        }
        if (count(self::$sqls) > 0) {
            echo '<br>【SQL语句】';
            foreach (self::$sqls as $sql) {
                echo '<li>&nbsp;&nbsp;&nbsp;&nbsp;' . $sql . '</li>';
            }
        }
        echo '</ul>';
        echo '</div>';
    }

}