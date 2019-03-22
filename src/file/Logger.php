<?php

namespace snapsuzun\yii2logger\file;


use snapsuzun\yii2logger\BaseLogger;
use yii\base\InvalidConfigException;
use yii\helpers\FileHelper;
use yii\helpers\VarDumper;

/**
 * Class Logger
 * @package snapsuzun\yii2logger\file
 */
class Logger extends BaseLogger
{
    /**
     * @var string log file path or [path alias](guide:concept-aliases). If not set, it will use the "@runtime/logs/app.log" file.
     * The directory containing the log files will be automatically created if not existing.
     */
    public $logFile;

    /**
     * @var int the permission to be set for newly created directories.
     * This value will be used by PHP chmod() function. No umask will be applied.
     * Defaults to 0775, meaning the directory is read-writable by owner and group,
     * but read-only for other users.
     */
    public $dirMode = 0775;

    /**
     * @var int the permission to be set for newly created log files.
     * This value will be used by PHP chmod() function. No umask will be applied.
     * If not set, the permission will be determined by the current environment.
     */
    public $fileMode;

    /**
     * @var int maximum log file size, in kilo-bytes. Defaults to 10240, meaning 10MB.
     */
    public $maxFileSize = 10240; // in KB
    /**
     * @var int number of log files used for rotation. Defaults to 5.
     */
    public $maxLogFiles = 5;

    /**
     * @var bool whether log files should be rotated when they reach a certain [[maxFileSize|maximum size]].
     * Log rotation is enabled by default. This property allows you to disable it, when you have configured
     * an external tools for log rotation on your server.
     */
    public $enableRotation = true;

    /**
     * @var bool Whether to rotate log files by copy and truncate in contrast to rotation by
     * renaming files. Defaults to `true` to be more compatible with log tailers and is windows
     * systems which do not play well with rename on open files. Rotation by renaming however is
     * a bit faster.
     *
     * The problem with windows systems where the [rename()](http://www.php.net/manual/en/function.rename.php)
     * function does not work with files that are opened by some process is described in a
     * [comment by Martin Pelletier](http://www.php.net/manual/en/function.rename.php#102274) in
     * the PHP documentation. By setting rotateByCopy to `true` you can work
     * around this problem.
     */
    public $rotateByCopy = true;

    /**
     * @throws \yii\base\Exception
     */
    public function init()
    {
        parent::init();
        if ($this->logFile === null) {
            $this->logFile = \Yii::$app->getRuntimePath() . '/logs/logger.log';
        } else {
            $this->logFile = \Yii::getAlias($this->logFile);
        }
        $logPath = dirname($this->logFile);
        if (!is_dir($logPath)) {
            FileHelper::createDirectory($logPath, $this->dirMode, true);
        }
        if ($this->maxLogFiles < 1) {
            $this->maxLogFiles = 1;
        }
        if ($this->maxFileSize < 1) {
            $this->maxFileSize = 1;
        }
    }

    /**
     * @param string $level
     * @param mixed $message
     * @param array $context
     * @throws InvalidConfigException
     */
    public function log($level, $message, array $context = [])
    {
        $date = date('Y-m-d H:i:s');
        $route = $this->getCurrentRoute();
        if (empty($route)) {
            $route = '-';
        }
        $context = $this->getCompiledTags();
        $text = strtr("date [level][route] message", [
            'date' => $date,
            'level' => $level,
            'route' => $route,
            'message' => $this->formatMessage($message, $context)
        ]);
        if (!empty($context)) {
            $text .= "\n \$tags = " . json_encode($context, JSON_PRETTY_PRINT);
        }
        $this->saveTextToFile($text);
    }

    /**
     * @param mixed $message
     * @param array $context
     * @return array|mixed|string
     */
    protected function formatMessage($message, array $context = [])
    {
        if (!is_string($message)) {
            if ($message instanceof \Throwable || $message instanceof \Exception) {
                $message = (string)$message;
            } else {
                $message = VarDumper::export($message);
            }
        } else {
            $message = $this->interpolate($message, $context);
        }

        return $message;
    }

    /**
     * Save text of log to file
     * @param string $text
     * @throws InvalidConfigException
     */
    protected function saveTextToFile(string $text)
    {
        $text .= PHP_EOL;
        if (($fp = @fopen($this->logFile, 'a')) === false) {
            throw new InvalidConfigException("Unable to append to log file: {$this->logFile}");
        }
        @flock($fp, LOCK_EX);
        if ($this->enableRotation) {
            clearstatcache();
        }
        if ($this->enableRotation && @filesize($this->logFile) > $this->maxFileSize * 1024) {
            $this->rotateFiles();
            @flock($fp, LOCK_UN);
            @fclose($fp);
            @file_put_contents($this->logFile, $text, FILE_APPEND | LOCK_EX);
        } else {
            @fwrite($fp, $text);
            @flock($fp, LOCK_UN);
            @fclose($fp);
        }
        if ($this->fileMode !== null) {
            @chmod($this->logFile, $this->fileMode);
        }
    }

    /**
     * Rotates log files.
     */
    protected function rotateFiles()
    {
        $file = $this->logFile;
        for ($i = $this->maxLogFiles; $i >= 0; --$i) {
            $rotateFile = $file . ($i === 0 ? '' : '.' . $i);
            if (is_file($rotateFile)) {
                if ($i === $this->maxLogFiles) {
                    @unlink($rotateFile);
                } else {
                    if ($this->rotateByCopy) {
                        @copy($rotateFile, $file . '.' . ($i + 1));
                        if ($fp = @fopen($rotateFile, 'a')) {
                            @ftruncate($fp, 0);
                            @fclose($fp);
                        }
                        if ($this->fileMode !== null) {
                            @chmod($file . '.' . ($i + 1), $this->fileMode);
                        }
                    } else {
                        @rename($rotateFile, $file . '.' . ($i + 1));
                    }
                }
            }
        }
    }
}