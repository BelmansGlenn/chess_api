<?php

namespace App\DTO\Paginator;

class PaginatorDTO
{
    private int $totalValue;
    private int $nbrPage;
    private array $data;
    private string $prev;
    private string $next;


    /**
     * @param array $data
     * @param $count
     * @param $countPage
     * @param $limit
     * @param $offsetPrev
     * @param $offsetNext
     * @param $url
     */
    public function __construct(array $data, $count, $countPage, $limit, $offsetPrev, $offsetNext, $url )
    {
        $this->totalValue = $count;
        $this->nbrPage = $countPage;
        $this->data = $data;
        if ($offsetPrev > 0){
            $this->prev = "$url?limit=$limit&offset=$offsetPrev";
        }else{
            $this->prev = "";
        }
        if ($offsetNext < $countPage * $limit){
            $this->next = "$url?limit=$limit&offset=$offsetNext";
        }else{
            $this->next = "";
        }


    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }



    /**
     * @return int
     */
    public function getTotalValue(): int
    {
        return $this->totalValue;
    }

    /**
     * @param int $totalValue
     */
    public function setTotalValue(int $totalValue): void
    {
        $this->totalValue = $totalValue;
    }

    /**
     * @return int
     */
    public function getNbrPage(): int
    {
        return $this->nbrPage;
    }

    /**
     * @param int $nbrPage
     */
    public function setNbrPage(int $nbrPage): void
    {
        $this->nbrPage = $nbrPage;
    }



    /**
     * @return string
     */
    public function getPrev(): string
    {
        return $this->prev;
    }

    /**
     * @param string $prev
     */
    public function setPrev(string $prev): void
    {
        $this->prev = $prev;
    }

    /**
     * @return string
     */
    public function getNext(): string
    {
        return $this->next;
    }

    /**
     * @param string $next
     */
    public function setNext(string $next): void
    {
        $this->next = $next;
    }



}