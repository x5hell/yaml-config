<?php

/** Интерфейс узла конфига */
interface InterfaceConfigNode
{
    /**
     * @param DateTime $actualDate фактическая дата
     */
    public function __construct(DateTime $actualDate = null);

    /**
     * @return DateTime фактическая дата
     */
    public function getActualDate();

    /**
     * @return array ассоциативный массив свойств
     */
    public function children();

}