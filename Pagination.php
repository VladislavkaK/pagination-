<?php
/*
 * Класс Pagination генерирует ссылки на страницы. Для настройки вида ссылок доступны различные параметры конфигурации PHP пагинации страниц.

        $baseURL–URL веб-страницы.
        $totalRows–общее количество элементов.
        $perPage–количество записей на странице.
        $numLinks–количество отображаемых ссылок.
        $firstLink–текст ссылки на первую страницу.
        $nextLink–текст ссылки на следующую страницу.
        $prevLink–текст ссылки на предыдущую страницу.
        $lastLink–текст ссылки на последнюю страницу.
        $fullTagOpen–открывающий тэг блока ссылок.
        $fullTagClose–закрывающий тэг блока ссылок.
        $firstTagOpen–открывающий тэг первого элемента.
        $firstTagClose–закрывающий тэг первого элемента.
        $lastTagOpen–открывающий тэг последнего элемента.
        $lastTagClose–закрывающий тэг последнего элемента.
        $curTagOpen–открывающий тэг текущего элемента.
        $curTagClose–закрывающий тэг текущег оэлемента.
        $nextTagOpen–открывающий тэг следующего элемента.
        $nextTagClose–закрывающий тэг следующего элемента.
        $prevTagOpen–открывающий тэг предыдущего элемента.
        $prevTagClose–закрывающий тэг предыдущего элемента.
        $numTagOpen–открывающий тэг числового элемента.
        $numTagClose–закрывающий тэг числового элемента.
        $showCount–показывать количество страниц.
 */

class Pagination
{
    private $baseUrl = '';
    private $totalRows = '';
    private $perPage = 10;
    private $numLinks = 2;
    private $currentPage = 0;
    private $firstLink = 'First';
    private $nextLink = 'Next &raquo;';
    private $prevLink = '&laquo; Prev ';
    private $lastLink = 'last';
    private $fullTagOpen = "<div class='pagination'>";
    private $fullTagClose = "</div>";
    private $firstTagOpen = '';
    private $firstTagClose = '&nbsp;';
    private $lastTagOpen = '&nbsp;';
    private $lastTagClose = '';
    private $curTagOpen = '&nbsp;<b>';
    private $curTagClose = '</b>';
    private $nextTagOpen = '&nbsp;';
    private $nextTagClose = '&nbsp;';
    private $prevTagOpen = '&nbsp;';
    private $prevTagClose = '';
    private $numTagOpen = '&nbsp;';
    private $numTagClose = '';
    private $showCount = true;
    private $currentOffset = 0;
    private $queryStringSegment = 'page';

    public function __construct($params = [])
    {
        if(count($params) > 0){
            $this->initialize($params);
        }
    }

    public function initialize($params = [])
    {
        if(count($params) > 0){
            foreach($params as $key => $val){
                if(isset($this->$key)){
                    $this->$key = $val;
                }
            }
        }
    }

    /*
     * Генерируем ссылки на страницы
     */
    public function createLinks()
    {
        //Если общее количество записей 0, не продолжать
        if($this->totalRows == 0 || $this->perPage == 0){
            return '';
        }

        //Считаем общее количество страниц
        $numPages = ceil($this->totalRows / $this->perPage);

        //Если страница только одна не продолжать
        if($numPages == 1){
            if($this->showCount){
                $info = 'Showing: '.$this->totalRows;

                return $info;
            } else {
                return '';
            }
        }

        //Определяем строку запроса
        $query_string_sep = (strpos($this->baseUrl, '?') === false) ? '?page=' : '&amp;page=';
        $this->baseUrl = $this->baseUrl.$query_string_sep;

        // Определяем текушую страницу
        $this->currentPage = $_GET[$this->queryStringSegment];

        if(!is_numeric($this->currentPage) || $this->currentPage == 0){
            $this->currentPage = 1;
        }

        // Строковая переменная вывода контента
        $output = '';

        // Отображаем сообщение о ссылках на другие страницы
        if($this->showCount){
            $currentOffset = ($this->currentPage > 1) ? ($this->currentPage-1) * $this->perPage : $this->currentPage;
            $info = 'Показаны элементы с '.$currentOffset.' по ';

            if(($currentOffset + $this->perPage) < $this->totalRows){
                $info .= $this->currentPage * $this->perPage;
            }else{
                $info .= $this->totalRows;
            }
            $info .= ' из '.$this->totalRows. ' | ';

            $output .= $info;
        }

        $this->numLinks = (int)$this->numLinks;

        // Если номер страницы больше максимального значения, отображаем последнюю страницу
        if($this->currentPage > $this->totalRows){
            $this->currentPage = $numPages;
        }

        $uriPageNum = $this->currentPage;

        // Рассчитываем первый и последний элементы
        $start = (($this->currentPage - $this->numLinks) > 0) ? $this->currentPage - ($this->numLinks - 1) : 1;
        $end = (($this->currentPage + $this->numLinks) < $numPages) ? $this->currentPage + $this->numLinks : $numPages;

        // Выводим ссылку на первую страницу
        if($this->currentPage > $this->numLinks){
            $firstPageURL = str_replace($query_string_sep, '', $this->baseUrl);
            $output .= $this->firstTagOpen.'<a href="'.$firstPageURL.'">'.$this->firstLink.'</a>'.$this->firstTagClose;
        }

        // Выводим ссылку на предыдущую страницу
        if($this->currentPage != 1){
            $i = $uriPageNum - 1;
            if($i == 0) {
                $i = '';
            }
            $output .= $this->prevTagOpen.'<a href="'.$this->baseUrl.$i.'">'.$this->prevLink.'</a>'.$this->prevTagClose;
        }

        // Выводим цифровые ссылки
        for($loop = $start - 1; $loop <= $end; $loop++){
            $i = $loop;
            if($i >= 1){
                if($this->currentPage == $loop){
                    $output .= $this->curTagOpen.$loop.$this->curTagClose;
                }else{
                    $output .= $this->numTagOpen.'<a href="'.$this->baseUrl.$i.'">'.$loop.'</a>'.$this->numTagClose;
                }
            }
        }

        // Выводим ссылку на следующую страницу
        if($this->currentPage < $numPages){
            $i = $this->currentPage + 1;
            $output .= $this->nextTagOpen.'<a href="'.$this->baseUrl.$i.'">'.$this->nextLink.'</a>'.$this->nextTagClose;
        }

        // Выводим ссылку на последнюю страницу
        if(($this->currentPage + $this->numLinks) < $numPages){
            $i = $numPages;
            $output .= $this->lastTagOpen.'<a href="'.$this->baseUrl.$i.'">'.$this->lastLink.'</a>'.$this->lastTagClose;
        }

        // Удаляем двойные косые
        $output = preg_replace("#([^:])//+#", "\1/", $output);

        // Добавляем открывающий и закрывающий тэги блока
        $output = $this->fullTagOpen.$output.$this->fullTagClose;

        return $output;
    }
}
