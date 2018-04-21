<?php

namespace YamlConfig;

/** Информация о yaml фрагменте */
class YamlPartInfo
{
    /** @var string имя фрагмента */
    protected $name;

    /** @var string комментарий */
    protected $comment;

    /** @var int уровень */
    protected $level;

    /**
     * @return string имя фрагмента
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name имя фрагмента
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string комментарий
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment комментарий
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return int уровень
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param int $level уровень
     */
    public function setLevel($level)
    {
        $this->level = $level;
    }
}